<?php namespace App\Core\V201\Request\Organization;

use App\Http\Requests\Request;
//use Illuminate\Http\Request;

class CreateNameRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        foreach ($this->request->get('name') as $key => $val) {
            $rules['name.' . $key . '.narrative'] = 'required|max:255';
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        $messages = [];
        foreach ($this->request->get('name') as $key => $val) {
            $messages['name.' . $key . '.narrative' . '.required'] = sprintf("Narrative %s is Required.", $key);
            $messages['name.' . $key . '.narrative' . '.max'] = sprintf("Max(255) Narrative %s .", $key);
        }
        return $messages;
    }

}
