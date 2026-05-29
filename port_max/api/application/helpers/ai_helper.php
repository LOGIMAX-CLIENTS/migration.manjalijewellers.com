<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AiHelper
{

    public static function explain($mainErr, $childErr, $skiped)
    {
        $mainErr = json_encode($mainErr, JSON_PRETTY_PRINT);
        $childErr = json_encode($childErr, JSON_PRETTY_PRINT);
        $skiped = json_encode($skiped, JSON_PRETTY_PRINT);
        $prompt = "
            You are helping a user fix Excel import errors.
            Main table errors : $mainErr
            Child table errors : $childErr
            Skiped rows : $skiped

            Explain in simple language and suggest a fix.
            Return max 3 to 5 lines.
            ";
        
        $content = "You explain Excel validation errors.";

        return self::callGemini($prompt);
    }

    public static function summary($summary)
    {
        $summaryJson = json_encode($summary, JSON_PRETTY_PRINT);

        $prompt = "
        You are generating an IMPORT TALLY SUMMARY report.

        Use ONLY the data provided in the INPUT DATA (JSON).
        Do NOT invent values.
        Do NOT explain anything.
        Output must be suitable for email.
        Use pipe `|` separated tables (NO markdown).

        ==============================
        INPUT DATA (JSON)
        ==============================
        {{SUMMARY_JSON}}

        ==============================
        REPORT FORMAT
        ==============================

        IMPORT TALLY SUMMARY

        --------------------------------------------------
        TALLY DONE ON | <current date and time>
        TALLY DONE FOR | From : <start date if available> To : <end date if available>
        DATA | SET 1 | CHIT DATA
        --------------------------------------------------

        ==============================
        SECTION 1 : RAW DATA
        ==============================

        Header:
        Raw Data | Data | Received Count | Received Amount | Valid Data Count | Valid Data Amount | Invalid Data Count | Invalid Data Amount

        Rows:
        - Create one row per logical data source found in raw_data
        - Use Entity as Raw Data
        - Use ExcelName as Data

        Values mapping:
        Received Count      = SheetRowCount
        Received Amount     = amount (if exists)
        Valid Data Count    = SheetRowCount - SkippedRowCount
        Valid Data Amount   = amount - invalid amount (if exists)
        Invalid Data Count  = SkippedRowCount
        Invalid Data Amount = invalid amount (if exists)

        Last Row:
        Total |  | Sum of Received Count | Sum of Received Amount | Sum of Valid Data Count | Sum of Valid Data Amount | Sum of Invalid Data Count | Sum of Invalid Data Amount

        ==============================
        SECTION 2 : IMPORTED DATA
        ==============================

        Header:
        Imported Data | Received Count | Received Amount | Live Count | Live Amount

        Rows:
        - Use currentImpData
        - One row per data source or branch found
        - Received Count = count
        - Live Count = count
        - Amount columns only if amount exists

        Last Row:
        Total | Sum | Sum | Sum | Sum

        ==============================
        SECTION 3 : BEFORE IMPORT DATA COUNT & TOTAL AMOUNT
        ==============================

        LEFT TABLE:
        Data / Source | Admin Count | Import Count | Total Count

        Rules:
        - Use beforeImpData
        - One row per branch (Branch <id_branch>)
        - Admin Count = count
        - Import Count = 0 unless explicitly present
        - Total Count = Admin Count + Import Count

        RIGHT TABLE (only if amount exists):
        Data / Source | Admin Amount | Import Amount | Total Amount

        ==============================
        SECTION 4 : AFTER IMPORT DATA COUNT & TOTAL AMOUNT
        ==============================

        LEFT TABLE:
        Data / Source | Admin Count | Import Count | Total Count

        Rules:
        - Use afterImpData
        - Same structure as BEFORE IMPORT

        RIGHT TABLE (only if amount exists):
        Data / Source | Admin Amount | Import Amount | Total Amount

        ==============================
        RULES
        ==============================
        - If amount field is NOT present in the data, do NOT show amount columns.
        - Maintain section titles exactly.
        - Totals must be calculated only from provided data.
        - No extra text.
        - No explanations.
        - Output tables only.
        ";

        $content = "You analyze Excel import tally reports and format them into structured tables.";

        return self::callGemini($prompt);
    }

    private static function callOpenAI($prompt,$content)
    {
        $ch = curl_init("https://api.openai.com/v1/chat/completions");

        $apiKey = getenv('OPENAI_API_KEY');

        if (!$apiKey) {
            return "❌ OPENAI_API_KEY not found in getenv()";
        }

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $apiKey,
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode([
                "model" => "gpt-4o-mini",
                "messages" => [
                    ["role" => "system", "content" => $content],
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.2
            ])
        ]);

        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true)['choices'][0]['message']['content'] ?? null;
    }

    private static function callGemini($prompt)
    {
        $apiKey = getenv('GEMINI_API_KEY');
            
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=".$apiKey;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true)['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
