<?php
require_once __DIR__ . '/RendererInterface.php';

final class UserFirstnameRenderer implements RendererInterface
{
    private static $VARIABLE_NAME = '[user:first_name]';
    private $applicationContext;

    public function __construct($applicationContext)
    {
        $this->applicationContext = $applicationContext;
    }

    public function needsToRender($text)
    {
        return false !== strpos($text, self::$VARIABLE_NAME);
    }

    public function render($text, $data)
    {
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $this->applicationContext->getCurrentUser();
        if (!$_user)
        {
            return $text;
        }

        return str_replace(
            self::$VARIABLE_NAME,
            ucfirst(mb_strtolower($_user->firstname)),
            $text
        );
    }
}