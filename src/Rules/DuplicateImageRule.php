<?php


namespace Photo\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;
use Photo\Models\Photo;

class DuplicateImageRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {

            if ($value instanceof UploadedFile && config('photo.allowDuplicate', true) === false) {
                $hasher = new ImageHash(new DifferenceHash());
                $hash = $hasher->hash($value);
                if (Photo::query()->where('hash', $hash)->first()) {
                    return false;
                }
            }

            return true;

        } catch (\Exception $exception) {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is already uploaded.';
    }
}
