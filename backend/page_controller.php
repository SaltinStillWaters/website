<?php
/**
 * Handles page access for client-side viewing.
 * 
 * Instructions:
 * - include the file
 * - call init(bool $isAccessible)
 * 
 * SESSION format:
 * $_SESSION[$SESSION_NAME] = [
 *  ['PREV_PAGE'] => string : previousPage,
 * 
 *  ['page_name_1'] => bool : isAccessible,
 *  ['page_name_2'] => bool : isAccessible,
 *  etc...
 * ]
 */
class PageController
{ 
    private static $FIRST_PAGE = 'login.php';
    private static $SESSION_NAME = 'pages';

    /**
     * Initializes Session variables needed to restrict access to page
     * 
     * @param bool $isAccessible specifies if page is accessible to the client for viewing by default.
     */
    public static function init(bool $isAccessible=true)
    {
        if (!isset($_SESSION[self::$SESSION_NAME]))
        {
            $_SESSION[self::$SESSION_NAME] = [];
        }
        
        if (!isset($_SESSION[self::$SESSION_NAME]['PREV_PAGE']))
        {
            $_SESSION[self::$SESSION_NAME]['PREV_PAGE'] = self::$FIRST_PAGE;
        }
        
        self::addScript($isAccessible);
        self::pageGuard();
        
        //must only be called after pageGuard()
        self::setPrevPage();
    }
    
    /**
     * Sets the previous page in session
     * Must only be called AFTER pageGuard()
     */
    private static function setPrevPage()
    {
        $_SESSION[self::$SESSION_NAME]['PREV_PAGE'] = self::getFileName();
    }
    /**
     * Sets whether a page is accessible or not
     * 
     * @param bool $canAccess accessibiility of the page
     * @param string $page the page to set the accessibility of. Leave blank if you want the current page
     */
    public static function setCanAccess(bool $canAccess, string $page=' ')
    {   
        $page = ($page==='') ? self::getFileName() : $page;
        
        $_SESSION[self::$SESSION_NAME][$page] = $canAccess;   
    }
    
    /**
     * Adds a session variable with the key equal to the page's name. Only does so if page is not already in session
     * Does nothing if page is already in session
     * Only used by init()
     * 
     * @param bool $isAccessible specifies i fthe added page is accessible to the client
     */
    private static function addScript(bool $isAccessible)
    {
        $fileName = self::getFileName();
        
        if (!isset($_SESSION[self::$SESSION_NAME][$fileName]))
        {
            $_SESSION[self::$SESSION_NAME][$fileName] = $isAccessible;
        }
    }

    /**
     * Checks if the cuurent page is accessible and returns its accessibility
     * @return bool returns accessibility of current page. If current page is not in session then returns false
     */
    private static function checkIfAccessible() : bool
    {
        $fileName = PageController::getFileName();
    
        if (!isset($_SESSION[self::$SESSION_NAME][$fileName]))
        {
            //echo is for testing only
            echo "$fileName is not defined in SESSION[self::\$SESSION_NAME] <br>";
            return false;
        }
        
        return $_SESSION[self::$SESSION_NAME][$fileName];
    }

    /**
     * Checks if the page is accessible and returns to the page specified by $_SESSION[$SESSION_NAME]['PREV_PAGE'] if it is not accessible
     */
    private static function pageGuard()
    {
        if (!self::checkIfAccessible())
        {
            header('Location: ' . $_SESSION[self::$SESSION_NAME]['PREV_PAGE']);
            exit();
        }
    }
    
    /**
     * extracts the current page's file name and returns it
     * @return string filename of the calling page
     */
    public static function getFileName() : string
    {
        $fileName = $_SERVER['PHP_SELF'];
        $fileName =  explode('/', $fileName);

        return end($fileName);
    }
}