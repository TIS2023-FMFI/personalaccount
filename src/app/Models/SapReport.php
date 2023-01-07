<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class SapReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'path',
        'uploaded_on'
    ];

    /**
     * Indicates if the model should be timestamped, using created_at and updated_at columns.
     *
     * @var mixed
     */
    public $timestamps = false;

    /**
     * Get the account with which the report is associated.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Generate a user-friendly name for the SAP report file represented by the model.
     * 
     * @return string
     * the generated file name
     */
    public function generateDisplayableFileName()
    {   
        $sanitizedSapId = $this->account->getSanitizedSapId();
        $contentClause = trans('files.sap_repport');
        $uploadedOn = Date::parse($this->uploaded_on)->format('d-m-Y');

        $fileName = "${sanitizedSapId}_${contentClause}_${uploadedOn}";

        return $this->appendFileExtension($fileName);
    }

    /**
     * Append the extension of the SAP report file represented by the model to a
     * file name. If the SAP report file has no extension, nothing is appended.
     * 
     * @param string $fileName
     * the file name to which to append the extension
     * @return string
     * the extended file name
     */
    private function appendFileExtension(string $fileName)
    {
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);

        return (empty($extension)) ? $fileName : $fileName . ".${extension}";
    }

    /**
     * Get the path to the directory within which a user's reports are stored.
     * 
     * @param User $user
     * the user whose directory for reports to consider
     * @return string
     * the path to the user's directory
     */
    public static function getReportsDirectoryPath(User $user)
    {
        return "reports/user_${user}";
    }
}
