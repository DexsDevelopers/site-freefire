<?php
function pdf_escape_text($text)
{
    $s = (string)$text;
    $s = str_replace("\\", "\\\\", $s);
    $s = str_replace("(", "\\(", $s);
    $s = str_replace(")", "\\)", $s);
    $s = str_replace(["\r\n", "\r"], "\n", $s);
    $s = str_replace("\n", " ", $s);
    return $s;
}

function pdf_simple_from_lines($title, $lines)
{
    $title = (string)$title;
    $lines = is_array($lines) ? $lines : [];

    $contentLines = [];
    $contentLines[] = "BT";
    $contentLines[] = "/F1 14 Tf";
    $contentLines[] = "50 800 Td";
    $contentLines[] = "16 TL";
    $contentLines[] = "(" . pdf_escape_text($title) . ") Tj";
    $contentLines[] = "T*";
    $contentLines[] = "/F1 11 Tf";
    $contentLines[] = "T*";

    $maxLines = 60;
    $i = 0;
    foreach ($lines as $line) {
        if ($i >= $maxLines) break;
        $contentLines[] = "(" . pdf_escape_text($line) . ") Tj";
        $contentLines[] = "T*";
        $i++;
    }
    $contentLines[] = "ET";
    $contentStream = implode("\n", $contentLines) . "\n";
    $contentLen = strlen($contentStream);

    $objects = [];
    $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    $objects[] = "<< /Length $contentLen >>\nstream\n$contentStream\nendstream";

    $out = "%PDF-1.3\n";
    $offsets = [0];
    for ($idx = 0; $idx < count($objects); $idx++) {
        $offsets[] = strlen($out);
        $objNum = $idx + 1;
        $out .= $objNum . " 0 obj\n" . $objects[$idx] . "\nendobj\n";
    }

    $xrefPos = strlen($out);
    $out .= "xref\n";
    $out .= "0 " . (count($objects) + 1) . "\n";
    $out .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objects); $i++) {
        $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $out .= "trailer\n";
    $out .= "<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $out .= "startxref\n";
    $out .= $xrefPos . "\n";
    $out .= "%%EOF";
    return $out;
}
