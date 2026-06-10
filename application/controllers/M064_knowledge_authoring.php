<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M064 Knowledge Base Authoring
 * Standalone CI3 controller. Routes:
 *   POST /coach/knowledge_create
 *   POST /coach/knowledge_edit
 *   GET  /coach/knowledge_list_v2
 *   GET  /coach/knowledge_get
 *   GET  /coach/knowledge_category_list
 *   POST /coach/knowledge_archive
 */
class M064_knowledge_authoring extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    private function _json($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }

    /**
     * Verify the Authorization Bearer header against the configured digest_token.
     * Returns true when valid, emits 401 and returns false when invalid.
     */
    private function _auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



// ---------------------------------------------------------------------------
// M064 endpoints
// ---------------------------------------------------------------------------

    /**
     * POST /coach/knowledge_create
     * Create a new knowledge artifact with M064 extended fields.
     * Required POST fields: title, uploaded_by_uid
     * Optional: artifact_type, target_segment, force_ack, expiry_date,
     *           body, file_url, uploaded_by_name, category, tags,
     *           parent_artifact_id, change_note
     */
    public function knowledge_create()
    {
        if (!$this->_auth()) return;

        $title        = trim((string)$this->input->post('title'));
        $atype        = trim((string)$this->input->post('artifact_type')) ?: 'doc';
        $target       = trim((string)$this->input->post('target_segment')) ?: 'all';
        $force_ack    = (int)$this->input->post('force_ack');
        $expiry       = $this->input->post('expiry_date') ?: null;
        $body         = (string)$this->input->post('body');
        $file_url     = (string)$this->input->post('file_url');
        $uploader_uid = (int)$this->input->post('uploaded_by_uid');
        $uploader_nm  = trim((string)$this->input->post('uploaded_by_name'));
        $category     = trim((string)$this->input->post('category'));
        $tags         = trim((string)$this->input->post('tags'));
        $parent_id    = (int)$this->input->post('parent_artifact_id') ?: null;
        $change_note  = trim((string)$this->input->post('change_note'));

        if (!$title) {
            $this->_json(array('ok' => false, 'error' => 'missing_title'), 400);
            return;
        }

        // Permission check: Super Admin (type_id=1) or AVP (type_id=24) only.
        if ($uploader_uid) {
            $u = $this->db->get_where('user', array('uid' => $uploader_uid))->row_array();
            if (!$u || !in_array((int)$u['type_id'], array(1, 24))) {
                $this->_json(array('ok' => false, 'error' => 'not_authorised'), 403);
                return;
            }
            if (!$uploader_nm) {
                $uploader_nm = trim($u['firstname'] . ' ' . $u['lastname']);
            }
        }

        $now = date('Y-m-d H:i:s');
        $row = array(
            'title'              => $title,
            'artifact_type'      => $atype,
            'target_segment'     => $target,
            'force_ack'          => $force_ack ? 1 : 0,
            'expiry_date'        => $expiry,
            'body'               => $body ?: null,
            'file_url'           => $file_url ?: null,
            'status'             => 'live',
            'uploaded_by_uid'    => $uploader_uid ?: null,
            'uploaded_by_name'   => $uploader_nm ?: 'Unknown',
            'category'           => $category ?: null,
            'tags'               => $tags ?: null,
            'version'            => 1,
            'parent_artifact_id' => $parent_id,
            'updated_at'         => $now,
            'last_editor_uid'    => $uploader_uid ?: null,
            'last_editor_name'   => $uploader_nm ?: 'Unknown',
        );
        $this->db->insert('knowledge_artifact', $row);
        $id = $this->db->insert_id();

        // Archive initial version into knowledge_artifact_version
        $this->db->insert('knowledge_artifact_version', array(
            'artifact_id'   => $id,
            'version'       => 1,
            'title'         => $title,
            'body'          => $body ?: null,
            'edited_by_uid' => $uploader_uid ?: 0,
            'edited_at'     => $now,
            'change_note'   => $change_note ?: 'Initial version',
        ));

        $this->_json(array(
            'ok'          => true,
            'artifact_id' => $id,
            'version'     => 1,
            'message'     => 'Artifact created and live for segment ' . $target,
        ));
    }

    /**
     * POST /coach/knowledge_edit
     * Edit an existing artifact. Increments version number and archives the
     * prior body into knowledge_artifact_version.
     * Required POST: artifact_id, editor_uid
     * Optional: title, body, category, tags, target_segment, force_ack,
     *           expiry_date, change_note
     */
    public function knowledge_edit()
    {
        if (!$this->_auth()) return;

        $artifact_id = (int)$this->input->post('artifact_id');
        $editor_uid  = (int)$this->input->post('editor_uid');

        if (!$artifact_id || !$editor_uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_artifact_id_or_editor_uid'), 400);
            return;
        }

        // Load existing row
        $existing = $this->db->get_where('knowledge_artifact', array('id' => $artifact_id))->row_array();
        if (!$existing) {
            $this->_json(array('ok' => false, 'error' => 'artifact_not_found'), 404);
            return;
        }
        if ($existing['status'] === 'archived') {
            $this->_json(array('ok' => false, 'error' => 'cannot_edit_archived_artifact'), 409);
            return;
        }

        // Resolve editor name
        $eu = $this->db->get_where('user', array('uid' => $editor_uid))->row_array();
        if (!$eu || !in_array((int)$eu['type_id'], array(1, 24))) {
            $this->_json(array('ok' => false, 'error' => 'not_authorised'), 403);
            return;
        }
        $editor_name = trim($eu['firstname'] . ' ' . $eu['lastname']);

        // Archive current version before applying changes
        $this->db->insert('knowledge_artifact_version', array(
            'artifact_id'   => $artifact_id,
            'version'       => (int)$existing['version'],
            'title'         => $existing['title'],
            'body'          => $existing['body'],
            'edited_by_uid' => (int)$existing['last_editor_uid'],
            'edited_at'     => $existing['updated_at'] ?: $existing['created_at'],
            'change_note'   => 'Auto-archived before version ' . ((int)$existing['version'] + 1),
        ));

        // Build update payload from supplied POST values
        $now        = date('Y-m-d H:i:s');
        $new_ver    = (int)$existing['version'] + 1;
        $update_row = array(
            'version'          => $new_ver,
            'updated_at'       => $now,
            'last_editor_uid'  => $editor_uid,
            'last_editor_name' => $editor_name,
        );

        $fields = array('title', 'body', 'category', 'tags', 'target_segment', 'expiry_date');
        foreach ($fields as $f) {
            $v = $this->input->post($f);
            if ($v !== null && $v !== false) {
                $update_row[$f] = $v;
            }
        }
        if ($this->input->post('force_ack') !== null && $this->input->post('force_ack') !== false) {
            $update_row['force_ack'] = (int)$this->input->post('force_ack') ? 1 : 0;
        }

        $this->db->where('id', $artifact_id)->update('knowledge_artifact', $update_row);

        // Log the new version in version table
        $this->db->insert('knowledge_artifact_version', array(
            'artifact_id'   => $artifact_id,
            'version'       => $new_ver,
            'title'         => isset($update_row['title']) ? $update_row['title'] : $existing['title'],
            'body'          => isset($update_row['body'])  ? $update_row['body']  : $existing['body'],
            'edited_by_uid' => $editor_uid,
            'edited_at'     => $now,
            'change_note'   => trim((string)$this->input->post('change_note')) ?: 'Updated',
        ));

        $this->_json(array(
            'ok'          => true,
            'artifact_id' => $artifact_id,
            'new_version' => $new_ver,
            'message'     => 'Artifact updated to version ' . $new_ver,
        ));
    }

    /**
     * GET /coach/knowledge_list_v2
     * List artifacts with optional filters: category, tags, segment, status.
     * Query params: category, tags (comma-separated), segment, status, limit, offset
     */
    public function knowledge_list_v2()
    {
        if (!$this->_auth()) return;

        $category = trim((string)$this->input->get('category'));
        $tags_raw = trim((string)$this->input->get('tags'));
        $segment  = trim((string)$this->input->get('segment'));
        $status   = trim((string)$this->input->get('status')) ?: 'live';
        $limit    = max(1, min(200, (int)($this->input->get('limit') ?: 50)));
        $offset   = max(0, (int)($this->input->get('offset') ?: 0));

        $this->db->where('status', $status);

        if ($category) {
            $this->db->where('category', $category);
        }
        if ($segment) {
            $this->db->group_start()
                     ->where('target_segment', 'all')
                     ->or_where('target_segment', $segment)
                     ->group_end();
        }
        if ($tags_raw) {
            $tag_list = array_filter(array_map('trim', explode(',', $tags_raw)));
            foreach ($tag_list as $tag) {
                $this->db->like('tags', $tag);
            }
        }

        // Count separately to avoid 'Not unique table/alias' when the same
        // table is referenced by count_all_results(false) and then get().
        $total = $this->db->count_all_results('knowledge_artifact', true);

        // Rebuild WHERE conditions for the data query.
        $this->db->where('status', $status);
        if ($category) {
            $this->db->where('category', $category);
        }
        if ($segment) {
            $this->db->group_start()
                     ->where('target_segment', 'all')
                     ->or_where('target_segment', $segment)
                     ->group_end();
        }
        if ($tags_raw) {
            $tag_list = array_filter(array_map('trim', explode(',', $tags_raw)));
            foreach ($tag_list as $tag) {
                $this->db->like('tags', $tag);
            }
        }

        $rows = $this->db->select('id, title, artifact_type, category, tags, version,
                                   target_segment, force_ack, expiry_date, status,
                                   uploaded_by_uid, uploaded_by_name, updated_at,
                                   last_editor_uid, last_editor_name')
                         ->limit($limit, $offset)
                         ->order_by('updated_at', 'DESC')
                         ->get('knowledge_artifact')
                         ->result_array();

        $this->_json(array(
            'ok'     => true,
            'total'  => $total,
            'limit'  => $limit,
            'offset' => $offset,
            'rows'   => $rows,
        ));
    }

    /**
     * GET /coach/knowledge_get?id=X
     * Fetch a single artifact plus its full version history.
     */
    public function knowledge_get($id = 0)
    {
        if (!$this->_auth()) return;

        $id = (int)($id ?: $this->input->get('id'));
        if (!$id) {
            $this->_json(array('ok' => false, 'error' => 'missing_id'), 400);
            return;
        }

        $artifact = $this->db->get_where('knowledge_artifact', array('id' => $id))->row_array();
        if (!$artifact) {
            $this->_json(array('ok' => false, 'error' => 'not_found'), 404);
            return;
        }

        $history = $this->db->where('artifact_id', $id)
                            ->order_by('version', 'DESC')
                            ->get('knowledge_artifact_version')
                            ->result_array();

        $this->_json(array(
            'ok'       => true,
            'artifact' => $artifact,
            'history'  => $history,
        ));
    }

    /**
     * GET /coach/knowledge_category_list
     * Returns the lookup table of available categories sorted by sort_order.
     */
    public function knowledge_category_list()
    {
        if (!$this->_auth()) return;

        $rows = $this->db->order_by('sort_order', 'ASC')
                         ->get('knowledge_category')
                         ->result_array();

        $this->_json(array('ok' => true, 'categories' => $rows));
    }

    /**
     * POST /coach/knowledge_archive
     * Soft-archive an artifact (sets status to 'archived').
     * Required POST: id
     */
    public function knowledge_archive()
    {
        if (!$this->_auth()) return;

        $id = (int)$this->input->post('id');
        if (!$id) {
            $this->_json(array('ok' => false, 'error' => 'missing_id'), 400);
            return;
        }

        $this->db->where('id', $id)
                 ->update('knowledge_artifact', array(
                     'status'      => 'archived',
                     'updated_at'  => date('Y-m-d H:i:s'),
                 ));

        $affected = $this->db->affected_rows();
        if (!$affected) {
            $this->_json(array('ok' => false, 'error' => 'not_found_or_already_archived'), 404);
            return;
        }

        $this->_json(array('ok' => true, 'message' => 'Artifact archived.'));
    }
} // end class M064_knowledge_authoring