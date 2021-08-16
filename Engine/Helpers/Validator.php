<?php


namespace app\Machine\Engine\Helpers;


use app\Machine\Engine\Cylinders\Read;

trait Validator
{

    abstract public function rules(): array;
    public array $errors = [];

    /**
     * @param $data
     */
    public function checkData($data)
    {
        foreach ($data as $key => $value){
            if(property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }


    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->rules() as $attr => $rules){

            $value = $this->{$attr};

            foreach ($rules as $rule){
                $ruleName = $rule;
                if (!is_string($ruleName)){
                    $ruleName = $rule[0];
                }

                if ($ruleName === RULE_REQUIRED && !$value){
                    $this->errorValidate($attr, RULE_REQUIRED);
                }

                if ($ruleName === RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->errorValidate($attr, RULE_EMAIL);
                }

                if ($ruleName === RULE_MIN && strlen($value) < $rule['min']){
                    $this->errorValidate($attr, RULE_MIN, $rule);
                }

                if ($ruleName === RULE_MAX && strlen($value) > $rule['max']){
                    $this->errorValidate($attr, RULE_MAX, $rule);
                }

                if ($ruleName === RULE_MATCH && $value !== $this->{$rule['match']}){
                    $this->errorValidate($attr, RULE_MATCH, $rule);
                }

                if ($ruleName === RULE_UNIQUE){
                    $uniqueAttr = $rule['fillable'] ?? $attr;
                    if ($this->uniqueChecker($uniqueAttr, $value)){
                        $this->errorValidate($attr, RULE_UNIQUE, ['field' => $attr]);
                    }
                }

                if ($ruleName === RULE_CHECK && $value !== $this->{$rule['check']}){
                    $this->errorValidate($attr, RULE_CHECK, $rule);
                }
            }
        }

        return empty($this->errors);
    }


    /**
     * @param string $attr
     * @param string $rule
     * @param array $params
     */
    private function errorValidate(string $attr, string $rule, array $params = [])
    {
        $message = errorMessage[$rule] ?? '';

        foreach ($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }

        $this->errors[$attr][] = $message;
    }

    public function getError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }

    public function has($attr)
    {
        return $this->errors[$attr] ?? false;
    }

    public function first($attr)
    {
        return $this->errors[$attr][0] ?? false;
    }


}