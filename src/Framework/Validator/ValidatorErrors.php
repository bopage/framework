<?php

namespace Framework\Validator;

class ValidatorErrors
{
    private $key;

    private $rule;

    private $attributes;

    private $messages = [
        'required' => "Le champ %s est requis",
        'empty' => "Le champ ne peut pas être vide",
        'slug' => "Le champ %s n\'est pas un slug valide",
        'minLength' => "Le champ %s doit contenir plus de %d caractères",
        'maxLength' => "Le champ %s doit contenir moins de %d caractères",
        'betweenLength' => "Le champ %s doit contenir entre %d et %d carctères",
        'datetime' => "Le champ %s doit être un datetime valide (%d)",
        'exist' => "La categorie %s n'existe pas dans la table %s",
        'unique' => "La champ %s doit être unique",
        'filetype' => "Le champ %s n'est pas au format valide (%s)",
        'email' => "Cet email n'est pas valide",
        'uploaded' => 'Vous devez uploader un fichier'
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        if (!array_key_exists($this->rule, $this->messages)) {
            return "le champs {$this->key} ne correspond pas à la règle {$this->rule}";
        } else {
            $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
            return (string)call_user_func_array('sprintf', $params);
        }
    }
}
