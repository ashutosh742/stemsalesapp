<?php
/**
 * MIGRATION 029 - Photo upload patch
 * =========================================================================
 * Adds two server-side computations to the existing photo upload pipeline:
 *   1. photo_size_kb  - file size in KB, written back to tblcallevents
 *   2. photo_phash    - 64-bit perceptual hash, hex-encoded, written back too
 *
 * TARGET FILE (merge into the existing controller, do not blindly replace):
 *   application/controllers/CallEventsController.php
 *     (or whichever existing controller currently handles photo upload for
 *      meeting events - check API_MAPPING.md migration 011 section)
 *
 * MERGE STRATEGY:
 *   1. Find the existing method that handles the photo POST.
 *      Typical names: upload_meeting_photo, save_event_photo, upload_image.
 *   2. After the existing $upload_path = ... line and file move logic,
 *      append the SNIPPET below right before the final DB update on
 *      tblcallevents.
 *   3. Make sure tblcallevents.photo_size_kb and tblcallevents.photo_phash
 *      columns exist (migration 029 SQL adds them).
 *
 * REQUIRES:
 *   - GD or Imagick PHP extension (gd is part of every standard PHP build).
 *     Most stemapp.in servers already have GD because MoM drafter uses it.
 *   - File system access to the upload path (the move_uploaded_file call
 *     already does this; we just stat the moved file).
 *
 * COST:
 *   - phash compute on a typical 200 KB JPEG: under 30 ms.
 *   - Negligible impact on upload latency.
 *
 * ROLLBACK:
 *   - Remove the snippet. The migration 029 scorer will fall back to
 *     'spoof_suspect' for events with null size + null phash, which is
 *     also the correct flag in a rollback scenario (we don't know the
 *     photo quality without the hash).
 * =========================================================================
 */


// ---------- BEGIN SNIPPET - paste into existing upload handler -------------

// Assumes $upload_path holds the absolute path to the saved file
// and $event_id holds the tblcallevents.id being updated.

$photo_size_kb = null;
$photo_phash   = null;

if ($upload_path && is_file($upload_path)) {
    $photo_size_kb = (int)round(filesize($upload_path) / 1024);
    $photo_phash   = $this->_compute_phash_64($upload_path);
}

$this->db->where('id', $event_id)->update('tblcallevents', [
    'photo_size_kb' => $photo_size_kb,
    'photo_phash'   => $photo_phash,
]);

// ---------- END SNIPPET ----------------------------------------------------



// ---------- SUPPORTING METHOD - add as private method in same controller ---

/**
 * Compute a 64-bit perceptual hash (pHash) of a JPEG/PNG file.
 * Returns 16 hex chars or null on failure.
 *
 * Algorithm: resize to 8x8 grayscale, compute mean, set bits where pixel > mean.
 * This is the standard average-hash variant. Cheap enough for upload-time use.
 * Two photos with the same hash are visually near-identical.
 */
private function _compute_phash_64($path)
{
    if (!function_exists('imagecreatefromjpeg')) return null;

    // Detect format
    $info = @getimagesize($path);
    if (!$info) return null;
    $img = null;
    switch ($info[2]) {
        case IMAGETYPE_JPEG: $img = @imagecreatefromjpeg($path); break;
        case IMAGETYPE_PNG:  $img = @imagecreatefrompng($path);  break;
        case IMAGETYPE_GIF:  $img = @imagecreatefromgif($path);  break;
        default: return null;
    }
    if (!$img) return null;

    // Resize to 8x8 grayscale
    $small = imagecreatetruecolor(8, 8);
    imagecopyresampled($small, $img, 0, 0, 0, 0, 8, 8, imagesx($img), imagesy($img));
    imagefilter($small, IMG_FILTER_GRAYSCALE);

    // Collect 64 luminance values
    $pixels = [];
    for ($y = 0; $y < 8; $y++) {
        for ($x = 0; $x < 8; $x++) {
            $rgb = imagecolorat($small, $x, $y);
            $pixels[] = $rgb & 0xFF; // grayscale so r=g=b, take any channel
        }
    }
    imagedestroy($small);
    imagedestroy($img);

    $mean = array_sum($pixels) / 64;

    // Build 64-bit hash as a binary string then convert to hex
    $bits = '';
    foreach ($pixels as $p) {
        $bits .= $p > $mean ? '1' : '0';
    }

    // Convert 64 bits to 16 hex chars
    $hex = '';
    for ($i = 0; $i < 64; $i += 4) {
        $nibble = substr($bits, $i, 4);
        $hex .= dechex(bindec($nibble));
    }
    return $hex; // 16 chars, fits CHAR(16) in tblcallevents.photo_phash
}

// ---------- END SUPPORTING METHOD ------------------------------------------



// ---------- GPS reading_time + accuracy patch ------------------------------
//
// The mobile app already POSTs gps_lat, gps_lng. After migration 029 the
// app also sends gps_accuracy_meters and gps_reading_time as separate POST
// fields. If your existing upload endpoint already accepts them, no work.
// If not, add this to the same upload method:

/*
$gps_accuracy_meters = $this->input->post('gps_accuracy_meters');
$gps_reading_time    = $this->input->post('gps_reading_time'); // YYYY-MM-DD HH:MM:SS in IST

if ($gps_accuracy_meters !== null || $gps_reading_time !== null) {
    $patch = [];
    if ($gps_accuracy_meters !== null) $patch['gps_accuracy_meters'] = (int)$gps_accuracy_meters;
    if ($gps_reading_time !== null)    $patch['gps_reading_time']    = $gps_reading_time;
    $this->db->where('id', $event_id)->update('tblcallevents', $patch);
}
*/

// The mobile app patch to send these fields lives in:
//   stem-crm-mobile/src/services/CallEventsAPI.js
// (small change - add two fields to the existing form data POST in
//  uploadMeetingPhoto). Not shipped in this bundle, ship with next mobile build.
