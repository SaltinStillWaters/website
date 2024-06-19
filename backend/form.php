<?php
require_once('backend/type.php');
/**
 * Handles all form text input.
 * Works together with type.php to validate input.
 * 
 * Instructions:
 * - include the file
 * - call init()
 * - use input() to take textual input
 * - use updateSession() BEFORE the form code
 * SESSION format:
 * $_SESSION[$SESSION_NAME] = [
 *  [id_1] => [content => 'content', error => 'error', type => 'type', required => 'required'],
 *  [id_2] => [content => 'content', error => 'error', type => 'type', required => 'required'],
 *  etc...
 * ]
 */
class Form
{
    public static $SESSION_NAME = 'user';
    public static function init()
    {
        if (!isset($_SESSION[self::$SESSION_NAME]))
        {
            $_SESSION[self::$SESSION_NAME] = [];
        }
    }
    public static function inputText(string $id, string $type, string $placeholder='', string $icon='', bool $required = false)
    {
        self::addSession($id, $type, $required);

        echo "  <div class='input-box'>
                    <input type='text' name='$id' placeholder='$placeholder' value='{$_SESSION[self::$SESSION_NAME][$id]['content']}'>
                    $icon
                    <span style='color: red;'> {$_SESSION[self::$SESSION_NAME][$id]['error']} <br></span>
                </div>";
    }
    public static function inputPassword(string $id, string $placeholder='', string $icon='', bool $required = false)
    {
        self::addSession($id, Type::$Password, $required);

        echo "  <div class='input-box'>
                    <input type='password' id='$id' name='$id' placeholder='$placeholder' value='{$_SESSION[self::$SESSION_NAME][$id]['content']}'>
                    $icon
                    <span style='color: red;'> {$_SESSION[self::$SESSION_NAME][$id]['error']} <br></span>
                    <input type='checkbox' id='checkbox_$id' name='checkbox_$id' onclick='myFunction(\"$id\")'>
                    <label for='checkbox_$id' class='custom-checkbox'></label>
                </div>";
    }


    private static function addSession(string $id, string $type, bool $required=false)
    {
        if (isset($_SESSION[self::$SESSION_NAME][$id]))
        {
            return;
        }
        
        $_SESSION[self::$SESSION_NAME][$id] = ['content' => '', 'error' => '', 'type' => $type, 'required' => $required];
    }
    public static function updateContents()
    {
        foreach ($_POST as $id => $content)
        {
            if ($id === 'submit')
            {
                continue;
            }
            
            if (isset($_SESSION[self::$SESSION_NAME][$id]))
            {
                $_SESSION[self::$SESSION_NAME][$id]['content'] = $content;
            }
        }
    }
    public static function updateErrors()
    {
        foreach ($_SESSION[self::$SESSION_NAME] as $id => $key)
        {
            
            if ($id == 'submit' || $id == 'refresh')
            {
                continue;
            }
            //check for invalid input
            if (!Type::checkValid($key['content'], $key['type']))
            {
                if (!str_contains($key['error'], Type::errMsg($key['type']) . '<br>'))
                {
                    $key['error'] .= Type::errMsg($key['type']) . '<br>';
                }
            }
            else
            {
                $key['error'] = str_replace(Type::errMsg($key['type']) . '<br>', '', $key['error']);
            }
            
            
            //check for blank input
            if (!$key['required'])
            {
                $_SESSION[self::$SESSION_NAME][$id]['error'] = $key['error'];
                continue;
            }
            
            if ($key['content'] === '')
            {
                if (!str_contains($key['error'], '*Required <br>'))
                {
                    $key['error'] .= '*Required <br>';
                }
            }
            else
            {
                $key['error'] = str_replace('*Required <br>', '', $key['error']);
            }

            
            //update session error
            $_SESSION[self::$SESSION_NAME][$id]['error'] = $key['error'];
            
        }
    }
}