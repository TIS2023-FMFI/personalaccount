<?php

namespace Tests\Feature\Operations;

use App\Http\Controllers\FinancialOperations\GeneralOperationController;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneralOperationControllerTest extends TestCase
{
    use DatabaseTransactions;

    private Model $user;
    private GeneralOperationController $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(['email' => 'a@b.c']);
        $this->controller = new GeneralOperationController;

    }

    public function test_file_name_generation(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $path = $this->controller->saveAttachment($this->user->id, $file);
        $expected = sprintf('user_%02d/attachments/attachment_0000',$this->user->id);

        $this->assertEquals($expected, $path);
        Storage::fake('local');

    }

    public function test_file_name_generation_with_existing_file(){

        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.txt');
        $this->controller->saveAttachment($this->user->id, $file);
        $path = $this->controller->saveAttachment($this->user->id, $file);
        $expected = sprintf('user_%02d/attachments/attachment_0001',$this->user->id);

        $this->assertEquals($expected, $path);
        Storage::fake('local');

    }

}
