<?php

namespace App\Rules;

use App\Support\PublicationAttachmentSecurity;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class SafePublicationAttachment implements Rule
{
    private ?string $failedFilename = null;

    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        if (! $value->isValid()) {
            return false;
        }

        if (! PublicationAttachmentSecurity::isAllowedUpload($value)) {
            $this->failedFilename = $value->getClientOriginalName();

            return false;
        }

        return true;
    }

    public function message()
    {
        return PublicationAttachmentSecurity::rejectionMessage($this->failedFilename);
    }
}
