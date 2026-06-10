<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Inputsanitizer - central clean-input layer (additive, 2026-06-06)
 *
 * One place to normalise and validate every user-supplied value that lands in
 * the database from data-entry flows (new leads, MOM, proposal details, PD
 * request, etc). Goal: only clean numeric data and clean text get stored. No
 * garbage like "4,66,100", "4.66LK", "INR 6.5 Lcas", "27", or junk remarks.
 *
 * Design rules:
 *   - NEVER throw. Always return a usable value plus a list of warnings so the
 *     caller decides whether to block or just clean. Existing saves never break.
 *   - Money is normalised to an integer count of rupees.
 *   - Indian formats handled: lakh / lac / lakhs / "lk", crore / "cr",
 *     comma grouping (4,66,100 and 466,100), spaces (4 66 100), currency words
 *     (Rs, INR, rupees), and decimal lakh shorthand (5.33 meaning 5.33 lakh
 *     when it is clearly a lakh-scale figure).
 *   - Text is trimmed, control chars stripped, length capped, and obvious junk
 *     (single char, only punctuation, keyboard mash) is flagged.
 *
 * ASCII only. "Rs" for rupees. "percent" spelled out. No em-dashes.
 */
class Inputsanitizer {

    /** Hard caps so a fat-fingered amount cannot store an absurd value. */
    private $money_min = 0;
    private $money_max = 100000000000; // Rs 10,000 crore ceiling

    public function __construct() {
        // no dependencies
    }

    // =====================================================================
    // MONEY
    // =====================================================================

    /**
     * Normalise a money string to an integer rupee value.
     * Returns array: ['value'=>int, 'display'=>string, 'ok'=>bool, 'warnings'=>[], 'raw'=>string]
     * value = 0 when the input is empty / NA / unparseable.
     */
    public function money($raw, $opts = array()) {
        $raw_in = (string)$raw;
        $s = trim($raw_in);
        $warn = array();

        if ($s === '' || strcasecmp($s, 'NA') === 0 || $s === '0') {
            return $this->_money_result(0, $raw_in, $warn, true);
        }

        $low = strtolower($s);

        // Detect multiplier from unit words / suffixes.
        $mult = 1;
        $has_cr  = (bool)preg_match('/\b(cr|crore|crores|cror)\b/i', $low) || preg_match('/[0-9]\s*cr\b/i', $low);
        $has_lk  = (bool)preg_match('/\b(lk|lac|lacs|lakh|lakhs|lcas|lkh)\b/i', $low) || preg_match('/[0-9]\s*lk/i', $low);
        if ($has_cr)      { $mult = 10000000; }
        elseif ($has_lk)  { $mult = 100000; }

        // Remove an abbreviation dot in a currency prefix like "Rs." or "Re."
        // so it is not mistaken for a decimal point on the amount.
        $low = preg_replace('/\b(rs|re|inr)\s*\./i', '$1 ', $low);

        // Strip everything except digits and dot.
        $num_str = preg_replace('/[^0-9.]/', '', $low);

        // A leading dot (e.g. from a stray ".50000") is not a real decimal.
        $num_str = ltrim($num_str, '.');
        // A trailing dot is noise.
        $num_str = rtrim($num_str, '.');

        // Guard against multiple dots (e.g. "5.33.00").
        if (substr_count($num_str, '.') > 1) {
            $parts = explode('.', $num_str);
            $num_str = array_shift($parts) . '.' . implode('', $parts);
            $warn[] = 'multiple_decimal_points_collapsed';
        }

        if ($num_str === '' || $num_str === '.') {
            $warn[] = 'no_numeric_content';
            return $this->_money_result(0, $raw_in, $warn, false);
        }

        $num = (float)$num_str;
        if ($num <= 0) {
            $warn[] = 'non_positive_amount';
            return $this->_money_result(0, $raw_in, $warn, false);
        }

        // Decimal-lakh shorthand: a bare small decimal like "5.33" with no unit
        // is almost always lakhs in this data set (5.33 lakh = 533000).
        if ($mult === 1 && strpos($num_str, '.') !== false && $num < 1000) {
            $mult = 100000;
            $warn[] = 'assumed_lakh_from_decimal_shorthand';
        }

        $value = (int)round($num * $mult);

        // Range guard.
        if ($value < $this->money_min) {
            $warn[] = 'below_min';
            $value = 0;
        }
        if ($value > $this->money_max) {
            $warn[] = 'above_max_capped';
            $value = $this->money_max;
        }

        // Sanity flag: a positive amount that resolves below Rs 1000 with no unit
        // is suspicious (the classic "27" garbage). Flag, do not silently keep.
        if ($value > 0 && $value < 1000 && $mult === 1) {
            $warn[] = 'suspiciously_small_amount';
        }

        $ok = empty(array_intersect($warn, array(
            'no_numeric_content','non_positive_amount','suspiciously_small_amount'
        )));

        return $this->_money_result($value, $raw_in, $warn, $ok);
    }

    private function _money_result($value, $raw, $warn, $ok) {
        return array(
            'value'    => (int)$value,
            'display'  => $value > 0 ? ('Rs ' . number_format($value)) : 'NA',
            'ok'       => (bool)$ok,
            'warnings' => array_values(array_unique($warn)),
            'raw'      => $raw,
        );
    }

    // =====================================================================
    // INTEGER COUNTS (no of schools, quantities)
    // =====================================================================

    /**
     * Normalise a count to a non-negative integer within [min,max].
     * Returns ['value'=>int, 'ok'=>bool, 'warnings'=>[], 'raw'=>string]
     */
    public function count_int($raw, $min = 0, $max = 100000) {
        $raw_in = (string)$raw;
        $s = trim($raw_in);
        $warn = array();
        if ($s === '' || strcasecmp($s, 'NA') === 0) {
            return array('value' => 0, 'ok' => true, 'warnings' => array(), 'raw' => $raw_in);
        }
        if (preg_match('/^\s*-/', $s)) { $warn[] = 'negative_sign_dropped'; }
        $digits = preg_replace('/[^0-9]/', '', $s);
        if ($digits === '') {
            $warn[] = 'no_numeric_content';
            return array('value' => 0, 'ok' => false, 'warnings' => $warn, 'raw' => $raw_in);
        }
        $v = (int)$digits;
        if ($v < $min) { $v = $min; $warn[] = 'below_min'; }
        if ($v > $max) { $v = $max; $warn[] = 'above_max_capped'; }
        return array('value' => $v, 'ok' => empty($warn), 'warnings' => $warn, 'raw' => $raw_in);
    }

    // =====================================================================
    // PHONE
    // =====================================================================

    /**
     * Normalise an Indian phone number to digits (last 10 kept, 91 prefix dropped).
     * Returns ['value'=>string, 'ok'=>bool, 'warnings'=>[], 'raw'=>string]
     */
    public function phone($raw) {
        $raw_in = (string)$raw;
        $digits = preg_replace('/[^0-9]/', '', $raw_in);
        $warn = array();
        // Drop leading country code variants.
        if (strlen($digits) === 12 && substr($digits, 0, 2) === '91') $digits = substr($digits, 2);
        if (strlen($digits) === 11 && substr($digits, 0, 1) === '0')  $digits = substr($digits, 1);
        $ok = (strlen($digits) === 10 && preg_match('/^[6-9]/', $digits));
        if (!$ok) $warn[] = 'not_a_valid_10_digit_mobile';
        return array('value' => $digits, 'ok' => $ok, 'warnings' => $warn, 'raw' => $raw_in);
    }

    // =====================================================================
    // EMAIL
    // =====================================================================

    public function email($raw, $allow_empty = true) {
        $raw_in = (string)$raw;
        $s = trim($raw_in);
        if ($s === '') {
            return array('value' => '', 'ok' => $allow_empty, 'warnings' => $allow_empty ? array() : array('empty'), 'raw' => $raw_in);
        }
        $ok = (bool)filter_var($s, FILTER_VALIDATE_EMAIL);
        return array('value' => $s, 'ok' => $ok, 'warnings' => $ok ? array() : array('invalid_email'), 'raw' => $raw_in);
    }

    // =====================================================================
    // TEXT / REMARKS
    // =====================================================================

    /**
     * Clean a free-text field (remark, MOM, event, address, company name).
     * - trims, collapses whitespace, strips control chars
     * - caps length
     * - flags junk: too short, only punctuation/digits when prose expected,
     *   or keyboard-mash repetition.
     * Returns ['value'=>string, 'ok'=>bool, 'warnings'=>[], 'raw'=>string]
     */
    public function text($raw, $opts = array()) {
        $raw_in = (string)$raw;
        $max_len   = isset($opts['max_len']) ? (int)$opts['max_len'] : 1000;
        $min_len   = isset($opts['min_len']) ? (int)$opts['min_len'] : 0;
        $is_prose  = !empty($opts['prose']); // remarks/MOM: expect real words
        $warn = array();

        // Strip control chars except newline/tab; collapse runs of whitespace.
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $raw_in);
        $s = preg_replace('/[ \t]+/', ' ', $s);
        $s = preg_replace('/\s*\n\s*/', "\n", $s);
        $s = trim($s);

        if ($s === '') {
            if ($min_len > 0) $warn[] = 'empty_required_text';
            return array('value' => '', 'ok' => ($min_len === 0), 'warnings' => $warn, 'raw' => $raw_in);
        }

        if (strlen($s) > $max_len) {
            $s = substr($s, 0, $max_len);
            $warn[] = 'truncated_to_max_len';
        }
        if ($min_len > 0 && strlen($s) < $min_len) {
            $warn[] = 'shorter_than_min_len';
        }

        if ($is_prose) {
            $letters = preg_match_all('/[A-Za-z]/', $s);
            // Junk: no letters at all (pure punctuation/digits) for a prose field.
            if ($letters === 0) $warn[] = 'no_letters_in_prose';
            // Junk: single repeated char like "aaaaaa" or "......".
            if (preg_match('/^(.)\1{4,}$/', $s)) $warn[] = 'repeated_single_char';
            // Junk: keyboard mash heuristic (long run of consonants, no vowel).
            if ($letters > 6 && !preg_match('/[aeiouAEIOU]/', $s)) $warn[] = 'no_vowels_possible_mash';
            // Junk: too short to be a real remark.
            if (strlen($s) < 3) $warn[] = 'too_short_for_remark';
        }

        $blocking = array('no_letters_in_prose','repeated_single_char','no_vowels_possible_mash','too_short_for_remark','empty_required_text');
        $ok = empty(array_intersect($warn, $blocking));
        return array('value' => $s, 'ok' => $ok, 'warnings' => array_values(array_unique($warn)), 'raw' => $raw_in);
    }

    /** Convenience: clean a company / person name (prose, shorter cap). */
    public function name($raw) {
        $r = $this->text($raw, array('prose' => true, 'max_len' => 255, 'min_len' => 2));
        // Names commonly have no vowels issue false positives on initials; relax that one.
        $r['warnings'] = array_values(array_diff($r['warnings'], array('no_vowels_possible_mash')));
        $r['ok'] = empty(array_intersect($r['warnings'], array('no_letters_in_prose','repeated_single_char','too_short_for_remark','empty_required_text')));
        return $r;
    }

    /** Convenience: URL / website (optional). */
    public function website($raw) {
        $raw_in = (string)$raw;
        $s = trim($raw_in);
        if ($s === '') return array('value' => '', 'ok' => true, 'warnings' => array(), 'raw' => $raw_in);
        if (!preg_match('#^https?://#i', $s)) $s = 'http://' . $s;
        $ok = (bool)filter_var($s, FILTER_VALIDATE_URL);
        return array('value' => $ok ? $s : trim($raw_in), 'ok' => $ok, 'warnings' => $ok ? array() : array('invalid_url'), 'raw' => $raw_in);
    }

    // =====================================================================
    // BATCH VALIDATION (for the preview endpoint)
    // =====================================================================

    /**
     * Validate a map of fields against a spec.
     * $spec example:
     *   ['proposal_amt'=>'money','noofschools'=>'count','remarks'=>'remark',
     *    'phoneno'=>'phone','emailid'=>'email','compname'=>'name']
     * Returns ['ok'=>bool, 'fields'=>[field=>result], 'blocking'=>[field,...]]
     */
    public function validate_map($data, $spec) {
        $out = array(); $blocking = array();
        foreach ($spec as $field => $type) {
            $val = isset($data[$field]) ? $data[$field] : '';
            switch ($type) {
                case 'money':  $r = $this->money($val); break;
                case 'count':  $r = $this->count_int($val); break;
                case 'phone':  $r = $this->phone($val); break;
                case 'email':  $r = $this->email($val); break;
                case 'name':   $r = $this->name($val); break;
                case 'website':$r = $this->website($val); break;
                case 'remark': $r = $this->text($val, array('prose' => true, 'min_len' => 3, 'max_len' => 2000)); break;
                case 'mom':    $r = $this->text($val, array('prose' => true, 'min_len' => 5, 'max_len' => 5000)); break;
                case 'text':
                default:       $r = $this->text($val, array('max_len' => 1000)); break;
            }
            $out[$field] = $r;
            if (!$r['ok']) $blocking[] = $field;
        }
        return array('ok' => empty($blocking), 'fields' => $out, 'blocking' => $blocking);
    }
}
