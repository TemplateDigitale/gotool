<?PHP
/******
 *  Functions collections
 *  ---------------------
 *  @author: Fulvio TemplateDigitale
 *  @web: http://www.templatedigitale.com
 */
class go_tool
{
  /***
   * pre_dump
   */
  public static function pre_dump( $array ){
    if( is_array($array) || is_object($array)){
      echo '<pre>';
      var_dump($array);
      echo '</pre>';
    }
  }

  /***
   * Clean Html
   */
  public static function clean_html($clean_it, $extraTags = '') {

    if (!is_array($extraTags)) $extraTags = array($extraTags);

    $clean_it = preg_replace('/\r/', ' ', $clean_it);
    $clean_it = preg_replace('/\t/', ' ', $clean_it);
    $clean_it = preg_replace('/\n/', ' ', $clean_it);

    $clean_it= nl2br($clean_it);

    // aggiorna le interruzioni inserendo uno spazio vuoto
    while (strstr($clean_it, '<br>'))   $clean_it = str_ireplace('<br>',   ' ', $clean_it);
    while (strstr($clean_it, '<br />')) $clean_it = str_ireplace('<br />', ' ', $clean_it);
    while (strstr($clean_it, '<br/>'))  $clean_it = str_ireplace('<br/>',  ' ', $clean_it);
    while (strstr($clean_it, '<p>'))    $clean_it = str_ireplace('<p>',    ' ', $clean_it);
    while (strstr($clean_it, '</p>'))   $clean_it = str_ireplace('</p>',   ' ', $clean_it);

    // pulisce i tag in array:
    $taglist = array('strong','b','u','i','em');
    $taglist = array_merge($taglist, (is_array($extraTags) ? $extraTags : array($extraTags)));
    foreach ($taglist as $tofind)
      if ($tofind != '') $clean_it = preg_replace("/<[\/\!]*?" . $tofind . "[^<>]*?>/si", ' ', $clean_it);

    // rimuove il doppio spazio creato su clean_it
    while (strstr($clean_it, '  ')) $clean_it = str_replace('  ', ' ', $clean_it);

    // rimuovi altri tag html
    $clean_it = strip_tags($clean_it);
    return trim($clean_it);

  }

  /***
   * Get Domain
   */
  public static function get_domain($url = ''){

    if( $url == '') $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
      return $regs['domain'];
    }
    return false;
  }

  /***
   * Dir List
   */
  public static function dir_list($dir){
      $l = array();
      foreach( array_diff(scandir($dir),array('.','..')) as $f)
        if(is_dir($dir.'/'.$f)) $l[]=$f;
      return $l;
  }

  /*
   * File List
   */
  public static function file_list($dir,$ext){
      $l = $file_parts = array();
      $php = array_diff( scandir( $dir ) , array('.','..') );
      foreach( $php AS $lstphp ){
        $file_parts = pathinfo($lstphp);
        if( isset($file_parts['extension']) ){
          if( strtolower($file_parts['extension']) === strtolower($ext) )
            $l[] = $lstphp;
        }
      }
      return $l;
  }

  /*
   * Remove Special chars
   */
  public static function sanitize($string) {
    $utf8 = array(
       '/[áàâãªä]/u'   =>   'a',
       '/[ÁÀÂÃÄ]/u'    =>   'A',
       '/[ÍÌÎÏ]/u'     =>   'I',
       '/[íìîï]/u'     =>   'i',
       '/[éèêë]/u'     =>   'e',
       '/[ÉÈÊË]/u'     =>   'E',
       '/[óòôõºö]/u'   =>   'o',
       '/[ÓÒÔÕÖ]/u'    =>   'O',
       '/[úùûü]/u'     =>   'u',
       '/[ÚÙÛÜ]/u'     =>   'U',
       '/ç/'           =>   'c',
       '/Ç/'           =>   'C',
       '/ñ/'           =>   'n',
       '/Ñ/'           =>   'N',
       '/–/'           =>   '-',
       '/[’‘‹›‚]/u'    =>   ' ',
       '/[“”«»„]/u'    =>   ' ',
       '/ /'           =>   ' ',
       );
       return preg_replace(array_keys($utf8), array_values($utf8), $string);
  }

  /*
   * Create Slug url
   */
  public static function slug( $string ){
    $string = self::sanitize(strtolower($string));//remove special chars
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    $string = preg_replace('~[^-\w]+~', '', $string);
    $string = preg_replace('~-+~', '-', $string);
    return str_ireplace(array(' ',',','&'), '-', $string );
  }
}
?>
