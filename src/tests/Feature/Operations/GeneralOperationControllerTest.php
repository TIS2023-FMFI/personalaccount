<?php

namespace Tests\Feature\Operations;

use App\Http\Controllers\FinancialAccounts\GeneralOperationController;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneralOperationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_file_name_generation(){

        Storage::fake('local');

        $controller = new GeneralOperationController;

        $dir = $controller->generateAttachmentDirectory(1);
        $this->assertEquals('user_1/attachments',$dir);

        $name = $controller->generateAttachmentName($dir);
        $this->assertEquals('attachment_0000',$name);

    }

    public function test_file_name_generation_with_existing_file(){

        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.txt');
        $path = Storage::putFileAs('user_1/attachments', $file, 'attachment_0000');
        $this->assertEquals('user_1/attachments/attachment_0000',$path);

        $controller = new GeneralOperationController;

        $dir = $controller->generateAttachmentDirectory(1);
        $this->assertEquals('user_1/attachments',$dir);

        $name = $controller->generateAttachmentName($dir);
        $this->assertEquals('attachment_0001',$name);

        Storage::fake('local');

    }

}
