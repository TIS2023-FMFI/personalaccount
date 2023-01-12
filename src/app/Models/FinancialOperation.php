<?php

namespace App\Models;

use App\Exceptions\DatabaseException;
use App\Http\Helpers\FileHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FinancialOperation extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Array of attributes excluded from mass assignation.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Array of related tables which should be eager-loaded from the DB along with this model.
     *
     * @var string[]
     */
    protected $with = ['operationType'];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'date' => 'date:d.m.Y',
    ];


    /**
     *  Extends a query asking for financial operations so that it demands only operations which represent expenses,
     *  and returns the new query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', true);
    }

    /**
     *  Extends a query asking for financial operations so that it demands only operations which represent income,
     *  and returns the new query builder.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIncomes(Builder $query): Builder
    {
        return $query
            ->join('operation_types', 'operation_type_id','=','operation_types.id')
            ->where('expense', '=', false);
    }

    /**
     * Returns the account to which this operation belongs.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Returns the type of this operation.
     *
     * @return BelongsTo
     */
    public function operationType()
    {
        return $this->belongsTo(OperationType::class);
    }

    /**
     * Returns whether this operation is an expense (whether it represents a negative sum).
     *
     * @return bool
     */
    public function isExpense()
    {
        return $this->operationType->expense;
    }

    /**
     * Returns whether this operation is a lending
     * (based on the operation type, not on existence of a DB 'lending' record).
     *
     * @return bool
     */
    public function isLending()
    {
        return $this->operationType->lending;
    }

    /**
     * Returns the lending record related to this operation, if it exists.
     *
     * @return HasOne
     */
    public function lending()
    {
        return $this->hasOne(Lending::class, 'id', 'id');
    }

    /**
     * Deletes the lending record related to this operation, if there is one.
     *
     * @throws DatabaseException
     */
    public function deleteLending()
    {
        if (! $this->isLending())
            return;
        if (! Lending::destroy($this->id))
            throw new DatabaseException('The lending wasn\'t deleted.');
    }

    /**
     * Generates the name of the directory where attachments for a user should be stored.
     *
     * @param $user
     * @return string
     * the generated name
     */
    public static function getAttachmentsDirectoryPath($user)
    {
        return "attachments/user_{$user->id}";
    }

    /**
     * Gets this operation's data in the order and format needed for a CSV export.
     *
     * @return array
     * an array filled with this operation's data
     */
    public function getExportData()
    {
        return [
            $this->id,
            $this->account->sap_id,
            $this->title,
            $this->date->format('d.m.Y'),
            $this->operationType->name,
            $this->subject,
            $this->getSumString(),
            $this->getCheckedString(),
            $this->sap_id
        ];
    }

    /**
     * Generates a string containing this operation's sum, with '-' after the number if it's an expense.
     *
     * @return string
     * the generated string
     */
    public function getSumString()
    {
        $sumString = sprintf('%.2f', $this->sum);
        if ($this->isExpense()) return "$sumString-";
        return $sumString;
    }

    /**
     * Generates an all-caps string describing whether this operation is checked,
     * or an empty string if this is a lending.
     *
     * @return string
     * the generated string
     */
    public function getCheckedString()
    {
        if ($this->isLending()) return '';
        return $this->checked ? 'TRUE' : 'FALSE';
    }

    /**
     * Creates a string containing this operation's title with only alphanumeric characters and dashes.
     *
     * @return string
     * the transformed title
     */
    public function getSanitizedTitle()
    {
        return FileHelper::sanitizeString($this->title);
    }

    /**
     * Generates a human-readable filename for this operation's attachment.
     *
     * @return string
     * the generated filename
     */
    public function generateAttachmentFileName()
    {
        $title = $this->getSanitizedTitle();
        $contentClause = trans('files.attachment');
        $fileName = "{$title}_$contentClause";

        return FileHelper::appendFileExtension($this->attachment, $fileName);
    }

}
