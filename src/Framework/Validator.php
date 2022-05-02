<?php
namespace Framework;

use DateTime;
use Framework\Validator\ValidatorErrors;

class Validator 
{
    private $params;

    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }
    
    /**
     * Definit les champs obligatoires
     *
     * @param  string $keys les champs requis
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }

        return $this;
    }
    
    /**
     * Vérifie que le champ n'est pas vied
     *
     * @param  string $keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }

        return $this;
    }

    
    /**
     * Vérifie la taille de la valeur de la clé
     *
     * @param  string $key
     * @param  int $min
     * @param  int $max
     * @return self
     */
    public function length(string $key, ?int $min = null, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (
            !is_null($min) && !is_null($max) && 
            $length < $min && $length > $max
        ) {
            $this->addError($key, 'betweeLength', [$min, $max]);
        }
        if (!is_null($min) &&  $length < $min) {
            $this->addError($key, 'minLength', [$min]);
        }
        if (!is_null($max) &&  $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }
    
    /**
     * Vérifie que le slug est valide
     *
     * @param  mixed $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^([a-z0-9]+-?)+$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }
    
    /**
     * Vérifie que la date est un datetime valide
     *
     * @param  string $key
     * @param  string $format
     * @return self
     */
    public function datetime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors['error_count'] > 0 ||  $errors['warning_count'] || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }
    
    /**
     * Renvoie les erreurs
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Vérifie s'il n'y a pas d'erreurs
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }
    
    /**
     * Ajoute une Erreur
     *
     * @param  string $key
     * @param  string $rule
     * @return void
     */
    private  function addError(string $key, string $rule, array $attributes = []) {
        $this->errors[$key] = new ValidatorErrors($key, $rule, $attributes);
    }
    
    /**
     * Renvoid la clé ou null
     *
     * @param  string $key
     * @return string|null
     */
    private function  getValue(string $key): ?string 
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}