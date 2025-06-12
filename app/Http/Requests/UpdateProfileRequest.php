
// app/Http/Requests/UpdateProfileRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
public function authorize(): bool
{
return true;
}

public function rules(): array
{
return [
'name' => ['sometimes', 'string', 'min:3', 'max:255'],
'email' => ['sometimes', 'email', 'unique:users,email,' . $this->user()->id, 'max:255'],
'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
'current_password' => ['required_with:password', 'string'],
];
}
}
