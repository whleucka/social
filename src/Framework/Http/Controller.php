<?php

namespace Echo\Framework\Http;

use App\Models\User;
use Echo\Framework\Session\Flash;
use Echo\Interface\Http\Controller as HttpController;
use Echo\Interface\Http\Request;
use Echo\Framework\View\TwigExtension;
use Error;

class Controller implements HttpController
{
    protected ?User $user = null;
    protected ?Request $request = null;
    private array $headers = [];
    private array $validation_errors = [];
    private array $validation_messages = [
        "required" => "Required field",
        "string" => "Must be a string",
        "array" => "Must be an array",
        "date" => "Invalid date format",
        "numeric" => "Must be a numeric value",
        "email" => "Invalid email address",
        "integer" => "Must be an integer",
        "float" => "Must be a floating-point number",
        "boolean" => "Must be a boolean value",
        "url" => "Invalid URL format",
        "ip" => "Invalid IP address",
        "ipv4" => "Must be a valid IPv4 address",
        "ipv6" => "Must be a valid IPv6 address",
        "mac" => "Invalid MAC address",
        "domain" => "Invalid domain name",
        "uuid" => "Invalid UUID format",
        "match" => "Does not match",
        "min_length" => "Input is too short",
        "max_length" => "Input is too long",
        "regex" => "Does not match pattern",
    ];

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getValiationErrors(): array
    {
        return $this->validation_errors;
    }

    /**
     * HTMX Trigger
     */
    public function hxTrigger(array|string $opts): void
    {
        if (is_array($opts)) {
            $opts = json_encode($opts);
        }
        $this->setHeader("HX-Trigger", $opts);
    }

    /**
    * HTMX Redirect
    */
    function redirect(string $path): void {
        if (request()->isHTMX()) {
            $this->setHeader("HX-Redirect", $path);
        } else {
            header("Location: " . $path);
            exit;
        }
    }

    /**
    * HTMX Location
    */
    function location(array|string $opts)
    {
        if (is_array($opts)) {
            $opts = json_encode($opts);
        }
        if (request()->isHTMX()) {
            $this->setHeader("HX-Location", $opts);
        } else {
            header("Location: " . $opts);
            exit;
        }
    }

    public function validate(array $ruleset): ?object
    {
        $valid = true;
        $request = $this->request->request->data();
        $data = [];

        foreach ($ruleset as $field => $set) {
            foreach ($set as $rule) {
                $r = explode(":", $rule);
                $rule = $r[0];
                $rule_val = $r[1] ?? null;
                $request_value = $request[$field] ?? null;
                $result = match($rule) {
                    'match' => $request_value == $request[$rule_val],
                    'min_length' => strlen($request_value) >= $rule_val,
                    'max_length' => strlen($request_value) <= $rule_val,
                    'required' => !is_null($request_value) && $request_value !== '',
                    'string' => is_string($request_value),
                    'array' => is_array($request_value),
                    'date' => strtotime($request_value) !== false,
                    'numeric' => is_numeric($request_value),
                    'email' => filter_var($request_value, FILTER_VALIDATE_EMAIL) !== false,
                    'integer' => filter_var($request_value, FILTER_VALIDATE_INT) !== false,
                    'float' => filter_var($request_value, FILTER_VALIDATE_FLOAT) !== false,
                    'boolean' => filter_var($request_value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null,
                    'url' => filter_var($request_value, FILTER_VALIDATE_URL) !== false,
                    'ip' => filter_var($request_value, FILTER_VALIDATE_IP) !== false,
                    'ipv4' => filter_var($request_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false,
                    'ipv6' => filter_var($request_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false,
                    'mac' => filter_var($request_value, FILTER_VALIDATE_MAC) !== false,
                    'domain' => filter_var($request_value, FILTER_VALIDATE_DOMAIN) !== false,
                    'uuid' => preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $request_value),
                    'regex' => preg_match("/$rule_val/", $request_value),
                    default => throw new \Error("undefined validation rule: $rule")
                };
                if ($result) {
                    $data[$field] = $request[$field];
                } else {
                    if (isset($this->validation_messages[$field.'.'.$rule])) {
                        $this->addValidationError($field, $this->validation_messages[$field.'.'.$rule]);
                    } else if (isset($this->validation_messages[$rule])) {
                        $this->addValidationError($field, $this->validation_messages[$rule]);
                    } else {
                        $this->addValidationError($field, "Invalid");
                    }
                }
                $valid &= $result;
            }
        }
        return $valid ? (object)$data : null;
    }

    protected function setValidationMessage(string $rule, string $message)
    {
        $this->validation_messages[$rule] = $message;
    }

    protected function addValidationError(string $field, string $message)
    {
        $this->validation_errors[$field][] = $message;
    }

    protected function getDefaultTemplateData(): array
    {
        return [
            "app" => config("app"),
            "flash" => Flash::get(),
            "validation_errors" => $this->validation_errors,
        ];
    }

    protected function render(string $template, array $data = []): string
    {
        $twig = twig();
        $data = array_merge($data, $this->getDefaultTemplateData());
        $twig->addExtension(new TwigExtension);
        return $twig->render($template, $data);
    }
}
