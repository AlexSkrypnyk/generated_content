<?php

namespace Drupal\generated_content\Helpers;

/**
 * Class GeneratedContentStaticTrait.
 *
 * Generic static content generators.
 *
 * @package Drupal\generated_content
 */
trait GeneratedContentStaticTrait {

  /**
   * Array of static content.
   *
   * @var string[]
   */
  protected static $staticContent;

  /**
   * Static content offset counter.
   *
   * Used to track calls to static content generator functions.
   *
   * @var int
   */
  protected static $staticOffset = 0;

  /**
   * Generate a pre-defined static sentence.
   *
   * @param int $count
   *   Number of words.
   *
   * @return string
   *   Static content string.
   */
  public static function staticSentence($count = 5) {
    $content = '';
    do {
      $content .= ' ' . static::staticParagraphs();
    } while (count(explode(' ', trim($content))) < $count);

    $words = explode(' ', trim($content));
    $words = array_slice($words, 0, $count);
    $content = implode(' ', $words);

    $content = rtrim($content, '.') . '.';

    return $content;
  }

  /**
   * Generates a static string.
   */
  public static function staticString($length = 32) {
    $content = '';
    do {
      $content .= preg_replace('/[^a-zA-Z0-9]/', '', static::staticParagraphs());
    } while (strlen($content) < $length);

    return strtolower(substr($content, 0, $length));
  }

  /**
   * Generates a static name.
   */
  public static function staticName($length = 16) {
    return static::staticString($length);
  }

  /**
   * Generates a letter abbreviation.
   *
   * @param int $length
   *   Length of abbreviation.
   *
   * @return string
   *   Abbreviation string.
   */
  public static function staticAbbreviation($length = 2) {
    return static::staticName($length);
  }

  /**
   * Generate a pre-defined static plain-text paragraph.
   *
   * @return string
   *   Static content string.
   */
  public static function staticPlainParagraph() {
    $content = static::staticParagraphs();

    return trim($content);
  }

  /**
   * Generate a pre-defined static HTML paragraph.
   *
   * @return string
   *   Static content string.
   */
  public static function staticHtmlParagraph() {
    return '<p>' . static::staticPlainParagraph() . '</p>';
  }

  /**
   * Generate a pre-defined static HTML heading.
   *
   * @param int $words
   *   Optional number of words. Defaults to 10.
   * @param int $level
   *   Optional heading level. Defaults to 1.
   * @param string $prefix
   *   Optional string prefix.
   *
   * @return string
   *   Static content string.
   */
  public static function staticHtmlHeading($words = 5, $level = 1, $prefix = '') {
    $level = min($level, 6);
    $level = max($level, 1);

    return '<h' . $level . '>' . $prefix . rtrim(static::staticSentence($words), '.') . '</h' . $level . '>';
  }

  /**
   * Generate a pre-defined static HTML content including headings.
   *
   * @param int $paragraphs
   *   Number of paragraphs to generate.
   * @param string $prefix
   *   Optional prefix to add to the very first heading.
   *
   * @return string
   *   Static content string.
   */
  public static function staticRichText($paragraphs = 4, $prefix = '') {
    $content = [];
    for ($i = 1; $i <= $paragraphs; $i++) {
      if ($i % 2) {
        $content[] = static::staticHtmlHeading(5, $i == 1 ? 2 : 3, $prefix);
      }
      $content[] = static::staticHtmlParagraph();
    }

    return implode(PHP_EOL, $content);
  }

  /**
   * Generate a pre-defined static set of plain-text paragraphs.
   *
   * @param int $paragraphs
   *   The number of paragraphs to create. Defaults to 10.
   * @param string $delimiter
   *   Optional delimiter index. Defaults to "\n\n".
   *
   * @return string
   *   Paragraphs as a static content string.
   */
  protected static function staticParagraphs($paragraphs = 1, $delimiter = "\n\n") {
    $content = static::$staticContent ?? static::staticContent();

    // Reset pointer once the end of the list is reached to allow
    // "endless" static content.
    if (self::$staticOffset > count($content) - 1) {
      self::$staticOffset = 0;
    }

    if ($paragraphs && $paragraphs > count($content)) {
      $paragraphs = count($content);
    }

    $content = array_slice($content, self::$staticOffset++, $paragraphs);

    return implode($delimiter, $content);
  }

  /**
   * Returns pre-defined static content.
   */
  protected static function staticContent() {
    return [
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ultrices vel sem ut blandit. Nulla lobortis arcu lacus, nec rhoncus leo mattis quis. Nulla vulputate semper mi, quis laoreet libero tincidunt non. In ac mattis risus. Fusce vehicula a est non suscipit. Morbi nec nunc nulla. Vivamus fringilla massa a diam vestibulum, in ornare nibh scelerisque. Pellentesque tristique ac magna sit amet vestibulum. Praesent ut nisi erat. Aliquam elementum arcu sed interdum elementum.',
      'Nunc mattis rutrum placerat. Proin lacinia, leo id feugiat viverra, augue magna gravida felis, vel hendrerit metus justo non massa. Quisque luctus molestie odio ut congue. Aenean vitae egestas tortor. Nam pharetra lorem erat, at ultricies nisl malesuada eu. Aliquam dictum pulvinar mi in iaculis. Cras a nulla a metus hendrerit tempor. Aliquam interdum nisl ac est elementum, quis feugiat nibh lobortis.',
      'Fusce cursus justo ac sem ornare, eget tempus nisi dignissim. Proin ac ligula urna. Pellentesque ut nisl sit amet libero tempor aliquam in facilisis odio. Integer semper, nisi a ullamcorper sollicitudin, augue orci ullamcorper felis, id porta mauris diam ut libero. Curabitur nec nisl sagittis, elementum sapien vel, pulvinar elit. Fusce at vulputate lacus, sit amet vestibulum elit. Integer suscipit dapibus leo et tempus. Donec efficitur ligula vel mauris sagittis, eu ultrices tellus sagittis. Mauris congue ante ligula, sed vehicula purus feugiat porttitor. Sed rutrum, lacus a viverra finibus, risus tellus fringilla dui, ac elementum neque diam a leo. Integer eget ante id libero mattis tincidunt. Maecenas ut nibh at ligula pretium ornare. Nunc eu lacus purus. Integer erat augue, fermentum et porta mollis, fermentum at urna. Fusce cursus ultricies enim ut gravida. Quisque ultricies mattis nibh at tincidunt.',
      'Maecenas sed pulvinar dui. Integer vel lectus at nisl ultrices iaculis. Nulla ut lacinia risus, eget dignissim nunc. Etiam metus lacus, dapibus at feugiat in, rutrum quis dolor. Donec commodo augue non enim posuere accumsan. Donec eu enim ante. Vivamus semper hendrerit enim, a eleifend magna euismod non. Nunc vulputate finibus eleifend. Nam lacinia est ut tristique consequat. Morbi in facilisis tortor.',
      'Duis at ipsum nec neque consectetur aliquam. Nam nec orci ultrices, molestie ligula sed, ullamcorper mauris. Etiam nisi quam, dapibus elementum ante nec, vulputate molestie elit. Integer vel mattis sapien. Aliquam erat volutpat. Phasellus molestie elementum mi quis rhoncus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur gravida mattis dolor viverra tristique. Phasellus blandit facilisis laoreet. Cras lacinia, risus vel sagittis commodo, leo felis porta velit, ac molestie mauris diam sed odio. Suspendisse pharetra purus eu mi maximus sollicitudin. Sed mollis, ligula ac vestibulum ullamcorper, eros urna auctor metus, ut aliquam turpis tortor et nisi. Phasellus id dui nec massa aliquet ultrices. Donec vitae auctor lectus. Sed augue dui, sagittis a mi a, consectetur volutpat sem.',
      'Nam faucibus orci a molestie iaculis. Donec vel mi id tellus consectetur dictum vitae eu nibh. Sed faucibus consectetur turpis, ut rutrum nisl consequat eget. Fusce risus arcu, auctor eget tempus quis, luctus eu felis. Praesent ac volutpat purus. Nunc placerat dui consequat nibh maximus rhoncus. Donec vestibulum quam in lacus ultrices posuere. Aenean finibus pellentesque risus.',
      'Vestibulum non feugiat diam. Aenean vel scelerisque massa. Aenean ac rhoncus leo. Morbi nisl nisl, pretium id ullamcorper et, rhoncus non urna. Nam nec nibh dapibus, porta dui eu, accumsan est. Proin urna metus, vehicula ut eros non, tincidunt vestibulum felis. Aenean ultricies turpis a arcu consectetur, non rhoncus quam imperdiet. Nulla nec est nec est sollicitudin varius hendrerit ut lacus. Vestibulum a leo id orci commodo auctor nec in libero. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin ut augue tempus, convallis lorem ac, mollis nisl. Cras luctus convallis augue eget malesuada. Nullam venenatis sodales leo, non dictum massa lobortis a. Vivamus tincidunt justo libero.',
      'Mauris consectetur maximus eros id iaculis. Quisque ut ante arcu. Quisque ac semper metus. Sed in ex id lectus varius dictum id in metus. Ut in congue urna, sit amet vestibulum mauris. Nunc eget risus finibus, faucibus diam at, auctor erat. Sed vehicula interdum sem, sed suscipit nisi hendrerit quis. Aliquam gravida metus mollis nisi posuere tempus vel vel nisl. Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque magna neque, congue aliquam fringilla non, dapibus commodo ante.',
      'Integer a sollicitudin odio. Cras vestibulum diam nisl, mollis ultrices nisi pulvinar et. Pellentesque et velit lorem. Mauris finibus urna nec facilisis euismod. Aenean tempor nibh dolor, nec accumsan leo tincidunt quis. Ut tempor nulla at eleifend mattis. Curabitur odio enim, tempus in consectetur vitae, sodales eget mi. Fusce rutrum sit amet enim in posuere. Sed vel tellus at enim finibus porta et faucibus urna. Nam efficitur maximus enim iaculis imperdiet. Donec in ullamcorper ante, a dapibus leo. Morbi eleifend sodales iaculis. Proin sed facilisis nisi, eget fringilla sapien.',
      'Suspendisse placerat vehicula magna at luctus. Fusce consequat vestibulum eros, ac convallis tellus consectetur sit amet. Morbi vel rhoncus leo, id convallis velit. In quis cursus diam. Phasellus vel dapibus velit. Fusce consectetur interdum metus, id varius felis facilisis vitae. Mauris vel nisl non arcu scelerisque bibendum. Integer in vehicula justo.',
      'Nam molestie nisi vitae facilisis ultrices. Curabitur ac pulvinar tellus. Phasellus ullamcorper viverra enim, ac varius metus dignissim at. Cras viverra sapien id tortor ultricies pretium. Suspendisse potenti. Pellentesque eleifend laoreet quam eu placerat. Nullam scelerisque nunc laoreet, imperdiet ipsum eu, malesuada ligula. Nulla suscipit, dolor eu efficitur scelerisque, velit felis egestas lorem, non porta ipsum mi vel nibh. Phasellus rhoncus tempus enim ut tincidunt. Vestibulum in lacinia est, eu rutrum magna. Sed in sem tempus, tempus nisl tristique, ultrices sapien.',
      'Etiam ullamcorper mollis imperdiet. Duis venenatis magna id consequat accumsan. Donec bibendum laoreet diam egestas mattis. Pellentesque fringilla tellus vitae eros lobortis, eu consequat eros blandit. Nunc blandit erat et arcu hendrerit, vitae blandit augue molestie. Nullam quis eros quam. Sed sagittis malesuada nisi ut pellentesque. Nulla facilisi. Fusce et elit quis nibh porta elementum. Nulla consectetur justo ac nulla blandit, non sagittis ipsum lacinia.',
      'Fusce molestie turpis in vehicula dignissim. Ut accumsan justo sed lacus convallis auctor. Pellentesque fermentum purus eu accumsan placerat. Duis sed orci orci. Fusce blandit, risus sit amet dictum aliquet, purus sapien malesuada urna, vel auctor dui massa et nibh. In nec mauris nibh. Nullam porttitor ac nunc quis placerat. Quisque et neque quis metus vestibulum vestibulum. Aenean vitae orci eros. Cras posuere velit sit amet elit dictum facilisis. Suspendisse vitae luctus orci. Fusce commodo diam a tortor gravida, auctor lobortis neque sagittis.',
      'Praesent vitae condimentum neque. Quisque mi elit, sagittis ac justo et, maximus consectetur nisi. Fusce id facilisis orci, quis ullamcorper turpis. Vestibulum nec ex consequat, hendrerit libero quis, vehicula ligula. Aenean eget nulla ultricies, elementum erat id, volutpat ligula. Integer eget tellus id sem lobortis mattis non at velit. Nam vitae faucibus velit, nec rhoncus magna. Integer vestibulum turpis ut pulvinar rhoncus. Ut a risus sit amet tellus dignissim bibendum quis vitae enim.',
      'Etiam imperdiet, massa sed interdum fermentum, nunc eros finibus quam, vel gravida enim urna quis massa. Nullam imperdiet in leo ac lobortis. Nulla quis nulla vitae ante cursus blandit sit amet a felis. Nunc viverra egestas nisi quis finibus. In eu ipsum vitae nisi volutpat volutpat eget non augue. Phasellus elementum velit massa, nec tempor sem porta ac. Proin rutrum, leo at condimentum sodales, velit ligula porta odio, nec auctor odio orci nec enim. Sed bibendum accumsan nulla, a aliquet enim varius et. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nam quis ultrices nunc, pulvinar sagittis magna.',
      'In porttitor tempus erat, vel maximus ligula tristique vitae. Morbi sit amet metus sit amet massa commodo consectetur. Phasellus molestie convallis lorem nec luctus. Phasellus eleifend dui ac congue rutrum. Ut faucibus elit sed ex suscipit, in mollis mauris porttitor. Maecenas placerat nisi nec ullamcorper ornare. Cras risus mi, malesuada sed interdum eu, tincidunt quis ipsum. In vel lorem leo.',
      'Curabitur id justo cursus, mattis metus at, consequat nisl. Quisque quis nulla a ante imperdiet dictum. Fusce eget sodales orci. Mauris id leo vel mauris ornare accumsan auctor a nulla. Fusce a justo aliquam, mollis erat vitae, sodales eros. Cras urna mauris, sagittis sed orci sed, eleifend dictum ligula. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dictum tortor eget sapien feugiat pretium. Ut dignissim placerat est in volutpat. Aliquam pulvinar augue vel aliquet facilisis. Integer nec aliquet dui, ut iaculis enim. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut pharetra lorem sed justo viverra, ut vehicula mi interdum. Nam libero nulla, condimentum non leo id, rhoncus hendrerit massa. Vivamus sit amet quam velit. Nulla facilisi. Nam eu neque mauris. Donec elementum, tortor eu laoreet vestibulum, sapien velit dapibus lectus, fermentum auctor elit libero id nulla. Phasellus vel vulputate justo, ut accumsan nibh. Aliquam et metus porta, posuere justo nec, consectetur lectus. Integer at metus pulvinar, condimentum mauris nec, hendrerit turpis.',
      'Quisque iaculis, dolor non viverra volutpat, risus lectus dignissim lorem, id pulvinar urna massa nec mauris. Maecenas arcu eros, varius vitae sollicitudin non, laoreet ac turpis. Nulla varius ante non lorem ultricies, eget facilisis nisi dictum. Pellentesque vitae aliquet tellus, sit amet vehicula urna. Nam sit amet est sit amet ante malesuada pretium. Sed auctor ut mauris sed euismod. Donec eget fermentum massa.',
      'Quisque malesuada gravida nibh, ac vehicula dolor lacinia euismod. Praesent consectetur justo ut justo scelerisque bibendum. Morbi lobortis sodales fermentum. In hac habitasse platea dictumst. Pellentesque ut tristique nulla. Morbi placerat risus in velit ultrices, vehicula vestibulum risus molestie. Donec vel felis eu neque fringilla faucibus id sit amet ex. Curabitur gravida, magna a elementum mattis, lacus eros congue enim, vitae ultrices augue purus ac arcu. In sed tempus purus, sit amet pulvinar nulla. Vivamus et pretium ante. Fusce eget commodo nulla. Ut a hendrerit turpis. Sed at metus eget velit mollis ornare. Morbi quis ligula dignissim, volutpat risus a, faucibus arcu. Proin euismod sodales nisl, eget faucibus mauris pharetra non.',
      'Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque efficitur vitae elit ut pharetra. Integer auctor ipsum quam, in tempus enim efficitur eu. Etiam mattis interdum libero. Nulla pellentesque nec leo lobortis ullamcorper. Nulla risus ipsum, venenatis ut interdum sed, vulputate id ligula. Duis rhoncus ac arcu nec vestibulum. Vivamus nulla erat, aliquet vel cursus non, venenatis in est. Proin id augue nibh. Aenean dapibus, urna nec tempor pretium, eros nibh posuere urna, sed semper nunc nisi non ex. Suspendisse dignissim risus libero. Praesent egestas vestibulum porttitor. Sed volutpat volutpat leo. Etiam sollicitudin volutpat auctor.',
      'Nunc pharetra, velit nec dapibus mollis, nisi est porta ipsum, quis varius orci justo vitae ex. Duis dictum, justo vitae aliquet eleifend, ipsum massa aliquet enim, a ultricies lacus tortor et ante. Maecenas sit amet risus odio. Nulla feugiat elit a lectus vulputate, sed pretium orci dignissim. Curabitur finibus quam at tortor cursus, a volutpat turpis luctus. Aenean a eros dapibus, maximus nulla non, fringilla tellus. Etiam in eros ac felis suscipit vestibulum eget eget ex. Sed commodo pharetra massa, id viverra lacus dictum finibus. Aliquam a scelerisque nunc. Nullam nisi lacus, efficitur at eros porttitor, finibus molestie dui. In sed tincidunt lectus. Curabitur nec dictum lectus, ut sollicitudin felis. Praesent ac erat sed turpis sollicitudin rhoncus. Nam quis erat quis elit pharetra placerat vitae et nibh. Maecenas auctor imperdiet nunc ac hendrerit.',
      'Nullam nibh ex, dapibus vitae mauris in, tempor sodales odio. In rhoncus ex orci, vel consequat ipsum ullamcorper id. Morbi a risus condimentum, rutrum purus non, suscipit diam. Vivamus non faucibus metus. Fusce eget neque pulvinar, congue quam vitae, gravida arcu. Nulla facilisi. Integer sodales rhoncus eros eget sagittis. Nulla porta lacus id mi egestas, hendrerit vehicula neque porttitor. Maecenas a consequat ligula. In malesuada ut augue et sagittis. Vivamus aliquam blandit tellus vitae pharetra.',
      'Etiam dictum, ipsum sit amet placerat sodales, quam urna mollis ante, eget auctor nisi tellus sed nunc. Nulla iaculis laoreet nulla, vitae congue quam iaculis id. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec non dui pretium libero aliquet convallis sed et magna. Curabitur laoreet purus ut nulla vehicula, in volutpat libero maximus. Cras sollicitudin dui sem, in venenatis mi elementum eget. Mauris iaculis magna sit amet congue varius. Proin a lacus rutrum, bibendum dolor sed, ultrices diam.',
      'Morbi tempor pulvinar rhoncus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc ullamcorper libero a velit lacinia sagittis. Cras interdum nisi vitae imperdiet rutrum. Morbi consequat est arcu, ut ullamcorper tellus tempus eget. Phasellus viverra elit feugiat justo tristique, quis ullamcorper erat lobortis. Sed porttitor convallis quam et tempus. In non lorem id arcu aliquam molestie eu non nisi. Quisque fermentum, lorem id ultrices viverra, neque felis blandit turpis, eget tincidunt arcu nulla porta orci. Vivamus interdum finibus viverra. Integer semper, erat vel hendrerit iaculis, ante purus facilisis dolor, sed rutrum elit leo sagittis lorem. Suspendisse sagittis neque dui, eu iaculis odio facilisis quis. Nullam blandit iaculis nunc vel posuere. Vestibulum leo urna, tempus a mauris sed, efficitur elementum eros. Cras vulputate metus eu nisl egestas sollicitudin.',
      'Suspendisse ullamcorper interdum orci sed accumsan. Donec et augue eu nisi rhoncus malesuada in ac metus. Mauris cursus felis vitae est efficitur, nec finibus nunc accumsan. Donec eget nulla vitae lectus feugiat vulputate. Vestibulum auctor odio ipsum, id tincidunt lorem fermentum eget. Morbi auctor, velit a laoreet egestas, erat elit hendrerit metus, volutpat sagittis libero tellus vitae erat. Nam a laoreet nisi, eu interdum risus. Aliquam euismod viverra mauris, et convallis tellus elementum ac. Suspendisse tincidunt nunc et pretium varius. Nunc tellus ante, fringilla nec tempor quis, aliquam vel leo. Donec a blandit diam. Donec vehicula neque nulla, eget consequat sem consequat eget. Nunc viverra facilisis nunc. Donec nec lacus et erat blandit fermentum. Vestibulum ultrices egestas ornare.',
      'Etiam vestibulum finibus lobortis. Etiam pretium nec massa non placerat. Phasellus suscipit eu urna a finibus. Phasellus eu interdum tortor. Integer non neque lobortis, fermentum mi a, rhoncus magna. Mauris volutpat vitae lacus ac gravida. Suspendisse nec orci id est mattis rhoncus ac ut velit. Morbi porta nec augue et fringilla. Nulla vitae pellentesque purus, non commodo sapien. Integer sodales lacus elit. Donec maximus eget mauris at posuere. Duis sodales metus vitae orci gravida, a aliquet odio porttitor. Duis ex mauris, consequat id nulla at, aliquam blandit dolor. Ut suscipit elit velit, imperdiet convallis tortor pellentesque et.',
      'Proin commodo mi ultricies risus ornare sodales. Ut posuere lacinia arcu nec vestibulum. Donec dignissim porta sapien sed commodo. Nam nulla enim, ornare sit amet pellentesque at, aliquam in orci. Maecenas vestibulum sed eros vitae laoreet. Nullam posuere nec erat vel dictum. Ut ultrices ipsum non metus tristique, at euismod quam scelerisque. Maecenas a purus at dui mollis pellentesque.',
      'Proin suscipit non ante et molestie. Fusce nec mi suscipit, malesuada elit ut, ultricies ante. Cras tincidunt sollicitudin ipsum sed hendrerit. Nullam eu venenatis enim. Vestibulum aliquet velit at urna maximus mollis. Suspendisse facilisis est a eros elementum, sit amet mattis erat efficitur. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut ut tristique magna. Vestibulum a mauris sit amet eros lacinia sodales.',
      'Nullam luctus leo ex. Proin diam diam, condimentum non convallis sit amet, pulvinar quis dolor. Nam eget risus egestas, consectetur ante molestie, suscipit nisl. Cras tristique molestie risus, a vehicula diam vestibulum eu. Phasellus scelerisque augue in nisi tincidunt, in ornare neque euismod. Suspendisse a aliquet neque. Fusce vehicula, tellus malesuada fermentum consectetur, metus dolor dignissim tortor, at aliquam nisl neque at mauris. Etiam tincidunt neque ante, pretium blandit massa pharetra sit amet. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla mattis massa libero, sed convallis purus vestibulum quis. Nunc posuere, ante in sagittis laoreet, ex nibh dictum nulla, tempus tempus dui ligula at felis. Ut ut velit augue. Curabitur in libero sit amet lacus sagittis eleifend.',
      'Maecenas tristique, dui sed ultricies pellentesque, urna purus dapibus neque, at condimentum neque nibh at ipsum. Etiam volutpat elit eu diam tristique ornare. Pellentesque sit amet lacus dictum, luctus ex a, ultricies ante. Aenean rhoncus tellus id facilisis molestie. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nullam eget mi id nisi consectetur auctor a et nisl. Donec ut posuere sapien. Nullam cursus vehicula rutrum. Praesent tortor justo, venenatis eu nisi ac, finibus commodo orci. Mauris varius sit amet ligula eget placerat. Maecenas et tincidunt odio. Praesent sagittis, nunc a dictum consequat, arcu lorem vestibulum libero, vitae semper diam lacus a purus.',
      'Ut dapibus fringilla malesuada. Sed vel ante non tellus convallis euismod. Pellentesque placerat risus sit amet ligula rhoncus, sed iaculis est sollicitudin. Nulla quis lacinia lectus. Sed imperdiet metus quis mattis viverra. Proin pulvinar ligula ac neque viverra interdum. Nulla eget libero nibh. Suspendisse id gravida diam. Nullam et est diam.',
      'Proin dapibus sapien aliquam ornare consequat. Fusce luctus placerat nunc. Aliquam et pellentesque sapien, at porttitor sapien. Nullam elementum sem ut rhoncus maximus. Nulla ut dui quis quam lacinia vulputate. Mauris velit ante, congue id iaculis sit amet, ultricies sed arcu. In tempus, tortor non tincidunt imperdiet, turpis nisl varius quam, sit amet hendrerit neque urna tempus metus. Donec feugiat ante et purus posuere bibendum in quis dui. Quisque in leo ut arcu dapibus sagittis. Phasellus gravida felis augue, hendrerit porta nulla tincidunt id. Nam lacinia gravida justo, ac vehicula augue ornare sit amet.',
      'Morbi vitae egestas massa. Vivamus nulla dolor, consequat at vestibulum quis, iaculis eu est. Quisque accumsan elit sed odio efficitur, ac congue ex accumsan. Cras eget turpis bibendum, vulputate lacus et, maximus orci. Nunc viverra tempor porta. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Vivamus eget tristique enim. Sed eu nunc urna. Donec dignissim massa orci, quis pellentesque nisl dictum et. Ut sit amet malesuada sem, a egestas eros. Duis purus nunc, congue et accumsan vel, aliquet vitae metus. Nulla pellentesque velit sem, quis pharetra est lacinia eget. Vivamus nisl ante, convallis non mattis vel, laoreet eget felis. Praesent sodales suscipit hendrerit.',
      'Phasellus ac eros nec massa sollicitudin dictum. Nulla nunc felis, luctus vel condimentum nec, efficitur id nisi. Nam ut justo eu odio cursus bibendum. Duis mauris odio, scelerisque bibendum mi sit amet, malesuada feugiat est. Morbi nec massa id dui mollis venenatis at et elit. Aenean scelerisque fermentum augue, et consequat eros sollicitudin vel. Morbi consectetur elementum nulla id efficitur. Aenean mollis vitae dolor nec vulputate. Fusce ut elit bibendum, molestie dui vitae, faucibus quam. Nunc ullamcorper a eros sed maximus.',
      'Nulla tincidunt arcu lectus, quis semper metus commodo sed. Nam lacinia lacus id nisl pretium egestas. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mattis mi ex, ut semper libero hendrerit ac. Pellentesque a faucibus risus. Curabitur gravida mauris eu arcu tristique blandit. Nulla a hendrerit enim.',
      'Sed aliquam ante ac nisl venenatis imperdiet. Morbi tincidunt lacus ac risus porta suscipit. Donec tempor sodales congue. Mauris pulvinar auctor justo in placerat. Duis dictum, erat sit amet bibendum faucibus, massa justo tincidunt metus, ut iaculis mauris risus in ligula. Nulla nulla orci, sollicitudin a ante a, porttitor gravida dolor. Pellentesque magna lorem, volutpat sed consectetur in, sagittis eget nisl. Nulla non ante sed nunc malesuada facilisis iaculis eu augue. Nulla tincidunt dui vitae ligula vestibulum, et condimentum tortor aliquam. Ut porttitor cursus ligula, vitae bibendum massa dictum eu. Cras vestibulum dignissim vestibulum. Phasellus sed arcu laoreet, interdum mi at, molestie risus. Phasellus quis magna sed nisl aliquam tincidunt. Phasellus luctus purus quis turpis bibendum, et feugiat arcu dictum.',
      'Cras ultrices neque sit amet scelerisque consectetur. Vivamus ac nibh iaculis, malesuada ante sed, vulputate turpis. Phasellus ultricies nisl aliquam, sagittis ex nec, porta leo. Cras fermentum risus id pulvinar sollicitudin. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque fermentum malesuada metus eu malesuada. Nunc vel lacus vel lectus faucibus dictum. Proin volutpat ex et gravida varius.',
      'Suspendisse eros ligula, placerat nec tempor at, placerat sit amet orci. Curabitur lacinia egestas orci quis porta. Praesent faucibus lacus non augue rutrum consectetur. Mauris vel vehicula justo. Maecenas odio tortor, euismod vitae nunc sed, porttitor aliquet sapien. Sed non feugiat ligula. Sed malesuada ante sit amet vulputate tempor. Morbi id sodales diam. Maecenas consectetur eget orci a egestas.',
      'Duis vel lectus eros. In hac habitasse platea dictumst. Nam porttitor imperdiet lectus eu luctus. Suspendisse potenti. Maecenas eu turpis mollis, scelerisque dolor vel, bibendum augue. Sed mattis eros ut hendrerit porttitor. Sed lacinia consectetur nibh, et rutrum metus placerat sollicitudin. Donec facilisis magna sed magna malesuada rhoncus. Ut semper, mi vel vehicula consectetur, sapien nisi scelerisque sem, sit amet interdum ante sapien a magna. Sed gravida varius aliquet. Etiam efficitur id nulla vehicula egestas. Vivamus placerat velit a nisi consectetur tempor. Morbi lorem dolor, tincidunt eu faucibus eu, tincidunt id est. Donec erat ligula, ultrices sed egestas vel, sodales in lectus. Suspendisse tincidunt, massa vel aliquam vulputate, ante sapien vulputate leo, sed mollis nisi diam vel odio.',
      'Quisque fermentum vulputate justo, non ultricies orci pharetra quis. Cras interdum iaculis rutrum. Maecenas malesuada neque varius metus vulputate, sed tempus ligula euismod. Integer vehicula sem ut ex eleifend condimentum. Donec vitae suscipit mi, vel mattis odio. Vestibulum ac justo ac odio consectetur egestas. Sed dolor dui, maximus non malesuada volutpat, pulvinar et orci. Nulla vestibulum, turpis eu tempor convallis, velit libero commodo lorem, quis finibus metus libero eget sapien.',
      'Aliquam vel magna leo. Aenean malesuada ultrices elit, dignissim luctus lacus finibus sed. Maecenas semper odio in mi ultrices facilisis. Maecenas fermentum tincidunt odio, at congue est maximus ut. Donec bibendum volutpat porta. Maecenas sodales egestas leo, nec commodo urna efficitur quis. Pellentesque sit amet sollicitudin massa. Fusce at urna dolor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer tincidunt, dolor vitae blandit elementum, tortor nunc cursus arcu, id laoreet ante enim quis mi.',
      'Integer tincidunt non lorem in fermentum. Nam est augue, lobortis id ipsum at, luctus consectetur urna. Morbi ornare vulputate aliquet. Sed at libero eu mauris suscipit mattis. Quisque eu tortor convallis, mollis leo quis, lobortis velit. Donec egestas lacinia iaculis. Nulla euismod risus ut leo scelerisque vehicula. Etiam bibendum purus lectus, ut interdum eros vulputate et. Morbi malesuada risus ornare, tincidunt urna in, suscipit neque. Nullam finibus sollicitudin turpis, at maximus mi rutrum ut. Integer ut magna in nibh aliquet dignissim in ac nisl. Maecenas blandit quis diam vitae consequat. Aliquam erat volutpat.',
      'Proin ipsum leo, convallis at porta et, suscipit et metus. Proin in imperdiet nulla. Suspendisse potenti. Suspendisse leo velit, molestie vitae odio vel, convallis eleifend est. Proin vitae semper dui, feugiat sodales sem. Aliquam sem turpis, cursus ac nunc vel, venenatis porttitor metus. In orci purus, commodo nec tortor eu, posuere hendrerit velit. Cras consectetur sollicitudin ligula nec egestas. Aliquam erat volutpat. Mauris tristique lectus arcu, ac volutpat nulla posuere vitae. Duis finibus hendrerit ante sed sodales.',
      'Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin sodales dictum risus, sit amet vestibulum felis dapibus id. Aliquam facilisis sodales lorem. Vestibulum et interdum mauris, at laoreet tortor. Suspendisse feugiat, nulla a posuere luctus, libero augue rutrum turpis, at placerat erat ipsum a tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Praesent a ex sollicitudin, vulputate nunc vel, mattis turpis. Cras ut gravida eros. Nunc quis facilisis mi, at facilisis felis. Ut vel metus libero. Donec et velit ex. Sed maximus condimentum lacus sed pretium. Praesent pulvinar urna vel dignissim viverra.',
      'Etiam ut gravida mi, vitae vehicula dolor. Vivamus facilisis nec arcu id iaculis. Maecenas ut felis cursus, viverra nulla eget, commodo magna. Morbi vel nulla et justo pretium interdum nec quis tortor. Ut et finibus tellus. Donec tincidunt justo quis faucibus malesuada. Praesent vitae tincidunt arcu, faucibus consequat enim.',
      'Phasellus a arcu velit. Praesent ac ante erat. Suspendisse nibh lorem, auctor eu nibh vel, ultricies placerat nisl. Donec eu dignissim dui. Curabitur efficitur lacinia metus, a porta sem luctus vel. Nunc non neque nibh. Sed consequat hendrerit quam, eu viverra ipsum placerat et. Quisque ante justo, aliquet id sollicitudin ut, feugiat in ante. Donec feugiat, nisi fermentum congue porttitor, arcu odio convallis est, ut molestie felis mi a massa. Nam ultricies eu quam eu tincidunt. Curabitur non rhoncus urna, id vestibulum augue.',
      'In in consequat risus. Nullam id tempus magna. Vestibulum dignissim tellus a augue fermentum ullamcorper. Donec convallis sodales ornare. Vestibulum eget diam nec mi rutrum pellentesque. Suspendisse potenti. Phasellus sed nisl vel enim venenatis condimentum eget non lorem. Nunc volutpat massa tempus orci dapibus, et vestibulum nisl vestibulum. Integer orci erat, pulvinar eu arcu eu, iaculis porttitor odio.',
      'Sed felis leo, posuere vitae turpis eu, imperdiet tempus turpis. Integer nisl turpis, ultrices vitae felis sed, cursus tincidunt ipsum. Sed maximus in ex eget dapibus. Nunc blandit sem lacus, non semper velit ultrices a. Vestibulum vehicula rhoncus velit, in porttitor turpis aliquet et. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent commodo lacinia gravida. Interdum et malesuada fames ac ante ipsum primis in faucibus. Quisque mauris dui, vehicula interdum iaculis ut, tincidunt vel felis. Cras pellentesque sem non pharetra posuere.',
      'Donec nisl lacus, sodales eget feugiat eget, lobortis vel erat. Morbi gravida lectus nec dui venenatis, eu malesuada elit pretium. Mauris fermentum nibh vitae sollicitudin dictum. Morbi nec diam volutpat, rhoncus erat pretium, molestie lectus. Nulla ipsum tellus, luctus nec placerat id, condimentum in ante. Nunc mi elit, condimentum vitae odio quis, bibendum elementum diam. Curabitur nec ipsum sed purus porta semper. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vestibulum rutrum leo sit amet vulputate accumsan. Vestibulum et lacus ac mi luctus lacinia. In tempor suscipit sollicitudin. Nam at diam id lectus elementum malesuada. Morbi ultrices suscipit tortor in consequat.',
    ];
  }

}
