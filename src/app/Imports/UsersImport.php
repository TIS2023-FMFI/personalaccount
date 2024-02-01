<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\OperationType;
use App\Models\SapOperation;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Importable;

class UsersImport implements ToModel, WithMultipleSheets
{
    use Importable;
    /**
     * Process a row from the Excel file and convert it into a SapOperation model.
     *
     * This method handles the conversion of each row from the Excel sheet into a
     * SapOperation model instance. It includes validation of required columns,
     * conversion of Excel date format to a standard date format, and handling
     * of operation type existence.
     *
     * @param array $row An array representing a single row from the Excel sheet.
     *                   The row data is expected to contain specific columns
     *                   like date, sum, title, etc., at predefined indices.
     *
     * @return ?SapOperation Returns an instance of SapOperation if the row is
     *                       processed successfully and meets all validation criteria.
     *                       Returns null if the row fails validation checks or
     *                       if any exception occurs during the process.
     *
     */
    public function model(array $row): ?SapOperation
    {

        $requiredColumns = [17, 3, 12, 10, 8];

        if ($row[17] === null || $row[17] === "") {
            return null;
        }

        $unixDate = ($row[17] - 25569) * 86400;
        $formattedDate = gmdate("Y-m-d", $unixDate);
        Log::warning($formattedDate);
        if (!$formattedDate) {
            Log::warning("Invalid date format in row. Skipping row.");
            return null; // Skip this row if date format is invalid
        }

        foreach ($requiredColumns as $index) {
            if (!isset($row[$index]) || $row[$index] === null) {
                Log::warning("Required column at index $index is null. Skipping row.");
                return null; // Skip this row
            }
        }

        $operationTypeId = 1; // The ID you're trying to use


        $operationTypeExists = OperationType::find($operationTypeId) !== null;

        if (!$operationTypeExists) {
            Log::warning("Operation type ID $operationTypeId does not exist. Skipping row.");
            return null; // Skip this row
        }

        $acc = Account::firstOrCreate(['sap_id' => $row[8]]);



        $sapOperation = new SapOperation([
            'date' => $formattedDate,
            'sum' => $row[3],
            'title' => $row[12],
            'operation_type_id' => $operationTypeId,
            'subject' => $row[10],
            'sap_id' => $row[0],
            'account_sap_id' => $row[8],
        ]);

        try {
            $sapOperation->save();
        } catch (Exception $e) {
            Log::error("Error saving SapOperation: " . $e->getMessage());
            return null; // Skip this row on error
        }

        return $sapOperation;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        // Only import the first sheet
        return [
            0 => new self(),
        ];
    }
}
