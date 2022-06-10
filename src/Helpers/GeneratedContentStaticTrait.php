<?php

namespace Drupal\generated_content\Helpers;

use Drupal\Component\Utility\Unicode;

/**
 * Class GeneratedContentStatic.
 *
 * Generic static content generators.
 *
 * @package Drupal\generated_content
 */
trait GeneratedContentStaticTrait {

  /**
   * Generate a pre-defined static sentence.
   *
   * @param int $words
   *   Number of words.
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   *
   * @return string
   *   Static content string.
   */
  public static function staticSentence($words = 10, $content_idx = 0) {
    $content = self::staticParagraphs(1, $content_idx);

    return Unicode::truncate($content, $words * 7, TRUE, FALSE, 3);
  }

  /**
   * Generate a pre-defined static plain-text paragraph.
   *
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   *
   * @return string
   *   Static content string.
   */
  public static function staticPlainParagraph($content_idx = 0) {
    $content = self::staticParagraphs(1, $content_idx);

    return trim($content);
  }

  /**
   * Generate a pre-defined static HTML paragraph.
   *
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   *
   * @return string
   *   Static content string.
   */
  public static function staticHtmlParagraph($content_idx = 0) {
    return '<p>' . self::staticPlainParagraph($content_idx) . '</p>';
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
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   *
   * @return string
   *   Static content string.
   */
  public static function staticHtmlHeading($words = 10, $level = 1, $prefix = '', $content_idx = 0) {
    $level = min($level, 6);
    $level = max($level, 1);

    return '<h' . $level . '>' . $prefix . self::staticSentence($words, $content_idx) . '</h' . $level . '>';
  }

  /**
   * Generate a pre-defined static HTML content including headings.
   *
   * @param int $paragraphs
   *   Number of paragraphs to generate.
   * @param string $prefix
   *   Optional prefix to add to the very first heading.
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   *
   * @return string
   *   Static content string.
   */
  public static function staticRichText($paragraphs = 10, $prefix = '', $content_idx = 0) {
    $content = [];
    for ($i = 1; $i <= $paragraphs; $i++) {
      if ($i % 2) {
        $content[] = self::staticHtmlHeading(8, $i == 1 ? 2 : 3, $prefix, $content_idx);
      }
      $content[] = self::staticHtmlParagraph($content_idx);
    }

    return implode(PHP_EOL, $content);
  }

  /**
   * Generate a pre-defined static set of plain-text paragraphs.
   *
   * @param int $paragraphs
   *   The number of paragraphs to create. Defaults to 10.
   * @param int $content_idx
   *   Optional content index. Defaults to 0.
   * @param string $delimiter
   *   Optional delimiter index. Defaults to "\n\n".
   *
   * @return string
   *   Paragraphs as a static content string.
   */
  protected static function staticParagraphs($paragraphs = 1, $content_idx = 0, $delimiter = "\n\n") {
    $content = static::staticContent();

    if ($paragraphs && $paragraphs > count($content)) {
      $paragraphs = count($content);
    }

    $content_idx = min(count($content) - 1, $content_idx);

    $content = array_slice($content, $content_idx, $paragraphs);

    return implode($delimiter, $content);
  }

  /**
   * Returns pre-defined static content.
   */
  protected static function staticContent() {
    return [
      "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque in ipsum id orci porta dapibus. Pellentesque in ipsum id orci porta dapibus. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a.",
      "Donec rutrum congue leo eget malesuada. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Donec sollicitudin molestie malesuada. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem.",
      "Quisque velit nisi, pretium ut lacinia in, elementum id enim. Sed porttitor lectus nibh. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Cras ultricies ligula sed magna dictum porta.",
      "Donec rutrum congue leo eget malesuada. Donec rutrum congue leo eget malesuada. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus.",
      "Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
      "Proin eget tortor risus. Nulla porttitor accumsan tincidunt. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Nulla porttitor accumsan tincidunt.",
      "Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Donec sollicitudin molestie malesuada. Nulla quis lorem ut libero malesuada feugiat. Quisque velit nisi, pretium ut lacinia in, elementum id enim.",
      "Nulla quis lorem ut libero malesuada feugiat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin eget tortor risus. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a.",
      "Sed porttitor lectus nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Sed porttitor lectus nibh. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula.",
      "Donec sollicitudin molestie malesuada. Vivamus suscipit tortor eget felis porttitor volutpat. Nulla quis lorem ut libero malesuada feugiat. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus.",
      'Fusce luctus id erat in vestibulum. Ut purus sem, aliquet sed orci ut, fringilla bibendum quam. Donec ut ipsum at nisl ultricies efficitur non id turpis. Mauris condimentum non libero tincidunt auctor. Curabitur enim enim, fermentum sit amet aliquam eu, mattis vel felis. Duis et massa accumsan, feugiat nibh nec, congue orci. Donec consectetur nunc mollis scelerisque laoreet. Integer sagittis ante et quam luctus blandit. Fusce a interdum diam. Proin vehicula tortor rutrum enim dictum, sed interdum est pharetra.',
      'Vestibulum et est sed felis molestie suscipit. Duis pretium eros et neque viverra porta. Duis vitae massa lacinia, pretium lectus ac, bibendum erat. Aliquam placerat malesuada lorem et vehicula. Morbi varius fermentum odio et condimentum. Aenean semper placerat purus vel varius. Donec sit amet purus fermentum quam tincidunt maximus sit amet sed nisl. Maecenas sed ornare lectus, vel accumsan dui. Integer dictum, neque in fringilla sodales, quam enim dapibus libero, in faucibus velit nulla vel turpis. Mauris feugiat sollicitudin egestas. Morbi sodales urna non viverra euismod. Nullam convallis velit fringilla odio posuere, id aliquam orci ornare. Nunc pretium sapien ac porttitor lobortis. Sed finibus laoreet elementum. Sed laoreet, tortor vel suscipit tempus, magna odio viverra tortor, ut sollicitudin augue mi eu ligula.',
      'Donec mattis orci vitae nisl tempor tincidunt. Nulla pellentesque nunc id eros tincidunt aliquam. Duis at enim vitae lectus venenatis imperdiet. Nulla hendrerit quam ac feugiat accumsan. Phasellus suscipit egestas dictum. Maecenas at tempor lectus, non dignissim arcu. Sed purus enim, varius a justo quis, aliquet dictum dolor. Cras laoreet, mauris vel porta molestie, erat ex pellentesque velit, dignissim aliquet sapien velit nec sapien. Integer vitae luctus eros. Praesent ex tortor, fermentum vel lacinia eu, hendrerit eget ligula. Cras eget porta nisl. Aliquam gravida finibus tortor in pulvinar. Quisque vel enim consectetur, laoreet tortor at, finibus est. Nam ac ornare purus. Duis egestas facilisis lacinia.',
      'Quisque eu congue ex, vitae elementum augue. Sed consectetur quis purus sit amet porttitor. Donec vehicula, neque eu tempor hendrerit, elit ex commodo sem, sit amet gravida tellus felis eu mi. Maecenas mauris mi, commodo et dignissim pellentesque, ultrices at quam. Aliquam non tincidunt justo, eu commodo tortor. Nunc eget consequat sapien, in finibus libero. Nulla mollis dolor in enim varius fermentum. Praesent augue massa, vulputate sed aliquet consectetur, suscipit et risus. Maecenas sed massa et purus posuere accumsan a sed augue. Nulla mollis vel mauris vel tincidunt. Nullam tincidunt orci ac libero suscipit tempor. Aliquam sapien metus, egestas nec diam ac, consectetur egestas est.',
      'Etiam luctus, ipsum eu tempus condimentum, tortor arcu facilisis ipsum, eu hendrerit urna risus in neque. Vestibulum malesuada, orci vel iaculis tristique, mi magna consectetur ante, sed pulvinar lorem velit sed purus. Quisque euismod sit amet lorem id efficitur. Donec pharetra non massa a lobortis. Donec iaculis convallis ipsum, et consectetur nibh interdum a. Phasellus et mi dignissim mauris egestas aliquet a sed ante. Nullam accumsan dapibus ante eget fringilla. Maecenas tristique metus sed lacus mollis, quis euismod nisi rutrum. Duis et massa eget eros egestas dignissim et a neque. Cras sollicitudin diam quam, at luctus dolor scelerisque nec. Donec dictum odio vitae venenatis faucibus. Donec elit metus, mattis vel lectus id, congue fringilla leo. Pellentesque nec massa nec mauris finibus interdum gravida sed ex. Ut nec magna nec tortor fringilla tincidunt. Mauris commodo feugiat interdum.',
      'Nulla enim purus, semper vel lobortis eget, condimentum sit amet lectus. Fusce congue fermentum erat eu mattis. Nulla facilisi. Etiam eu nulla at justo fermentum tristique. Ut faucibus id lacus vel gravida. Etiam convallis libero dolor, nec pharetra odio egestas eget. Vivamus id orci purus. Nullam aliquam congue viverra. Ut ut enim tempor, consectetur ligula eu, pellentesque orci. Vestibulum at diam vel mauris pellentesque elementum. Nulla vitae mattis nisi, id faucibus felis. Proin sit amet urna odio. Nam maximus quis leo nec pellentesque. In porttitor, metus eget luctus tincidunt, sapien nibh finibus lorem, ac rhoncus velit dolor varius lorem.',
      'Integer tempor nunc ac turpis maximus feugiat. Suspendisse ipsum nisl, dictum at aliquam sit amet, molestie eget erat. Phasellus vel est bibendum, ultrices ex vel, commodo justo. Nulla blandit sodales mi sagittis eleifend. Integer tincidunt turpis in ipsum dapibus elementum. Etiam sed enim tellus. Cras viverra ligula a orci condimentum luctus.',
      'Sed lobortis congue molestie. In et risus sit amet purus rutrum pellentesque. Praesent id ornare metus. Suspendisse potenti. Donec suscipit purus ac nunc scelerisque, a eleifend velit sollicitudin. Ut accumsan mollis nisi at dictum. Aliquam ante est, ornare sed tincidunt id, volutpat quis leo. Ut eleifend nunc nunc, sit amet luctus magna porta ut. Suspendisse potenti.',
      'Aenean nec turpis egestas nunc consectetur fermentum ac eget lorem. Aliquam erat volutpat. Etiam luctus nisl vitae est mattis, vel tempus ante aliquam. Maecenas eget pharetra odio, et viverra urna. Nullam at orci volutpat, aliquam ipsum sit amet, bibendum velit. Donec suscipit nec est in luctus. Cras sed nulla mauris. Donec volutpat ut sapien sed dictum. In pretium sollicitudin nulla, in congue arcu dapibus vel.',
      'Cras posuere lectus sit amet nisl eleifend, quis blandit magna laoreet. Etiam accumsan massa nisl, non finibus justo molestie id. Sed bibendum imperdiet commodo. Donec suscipit tincidunt leo et fringilla. Nulla aliquet dui vitae libero eleifend vehicula. Maecenas molestie scelerisque urna sed imperdiet. Vivamus vel rutrum sapien. Maecenas auctor lorem vel felis scelerisque dignissim. Nam ante tellus, posuere sit amet sodales et, finibus eget orci. Donec aliquet aliquam interdum. Maecenas ornare urna libero, ut ultrices ipsum placerat at. Suspendisse leo nisi, bibendum non justo viverra, iaculis ullamcorper magna. Aliquam orci ligula, interdum sit amet risus non, egestas malesuada metus.',
      'Suspendisse rhoncus risus urna, a tristique quam euismod vel. Curabitur gravida mauris eget leo luctus scelerisque. Curabitur eget dolor in ipsum bibendum dictum. Mauris id purus id tellus varius laoreet. Nullam tincidunt urna pulvinar, gravida ligula in, placerat eros. Cras id metus libero. Cras maximus consectetur eros a tincidunt. Donec ac lectus quam. In sed purus in felis hendrerit dictum. Morbi sed diam hendrerit, vulputate lectus sed, porta libero.',
      'Duis fermentum consequat velit, a porttitor neque luctus et. Duis nisi purus, tincidunt at scelerisque ac, accumsan id lacus. Integer pharetra, lectus sed lobortis vulputate, velit ligula eleifend nulla, quis sodales enim ex viverra diam. Nam ac dignissim velit. Fusce viverra rhoncus odio nec iaculis. Sed mattis nunc in eros luctus finibus. Cras ut varius urna. Nullam vel leo pulvinar, lacinia odio vel, pretium sapien. Integer ultrices, orci eu accumsan iaculis, quam nisi imperdiet dui, eget blandit neque quam malesuada magna. Integer nec nulla ut sapien dictum congue sed eget mi. Maecenas fringilla metus sed mauris sagittis dictum. Pellentesque efficitur congue arcu et dapibus.',
      'Aenean est erat, ullamcorper ut aliquet sit amet, hendrerit pretium odio. Nulla facilisi. Praesent nec nisl eget leo tempor malesuada. Mauris ut massa sem. Morbi leo turpis, ultricies non commodo nec, volutpat ac tortor. Aenean id leo nec magna commodo feugiat in nec quam. Donec imperdiet nibh arcu, vel porttitor augue dictum et. Etiam faucibus faucibus lacus, eu sollicitudin leo consequat posuere.',
      'Pellentesque condimentum ante sed nunc molestie mattis. Vivamus fermentum vestibulum sem, blandit viverra velit molestie elementum. Nunc imperdiet nisl in dapibus varius. Vestibulum at lectus in mauris mollis placerat. Curabitur vulputate dui id elit faucibus, quis vulputate turpis pellentesque. Sed euismod metus sit amet lectus aliquam dapibus. Proin consectetur, odio eu tempus blandit, neque eros efficitur nulla, nec tincidunt ante purus semper velit. Nulla elementum egestas ipsum ac tincidunt. Interdum et malesuada fames ac ante ipsum primis in faucibus. Praesent commodo ac ligula id eleifend. Aliquam erat volutpat.',
      'Vestibulum accumsan lorem sit amet orci porttitor, in consectetur diam scelerisque. Fusce lectus nisl, venenatis quis metus eget, lacinia mattis metus. Morbi scelerisque vulputate arcu. Praesent posuere in est vitae commodo. Etiam sodales urna id quam dictum, at dapibus nisi tristique. Ut ac hendrerit purus. Suspendisse aliquet imperdiet eleifend. Fusce tincidunt arcu ut sapien venenatis, ut semper risus efficitur. Sed eu mollis ipsum. Suspendisse sodales auctor justo at lobortis. Integer ultrices pretium elit, sed tincidunt felis tempus vitae. Nunc neque arcu, posuere vel commodo vel, accumsan eget magna. In ornare nunc ac orci ultrices ullamcorper.',
      'Phasellus mattis mauris quis sem tincidunt, vestibulum eleifend mauris ultricies. Curabitur pharetra odio vitae varius ultrices. Curabitur tempor lacinia mi, nec porttitor leo malesuada id. Cras mollis in justo congue dapibus. Proin nec maximus urna. Vivamus convallis, massa ut auctor porta, leo tellus imperdiet quam, non auctor mauris elit non augue. Fusce quis lacus lacus. In at ipsum eget neque laoreet accumsan. Vestibulum tincidunt in risus sit amet ultrices. Nam hendrerit, mauris fringilla tempor egestas, ex risus ultricies magna, nec tincidunt mauris nibh sed nulla. Vestibulum pulvinar auctor nunc, in egestas mauris vestibulum a. Morbi mattis ligula vitae nunc sagittis, quis scelerisque tortor vehicula. Phasellus ac sollicitudin est.',
      'Quisque ultricies faucibus sollicitudin. Nam at nisl velit. Aenean in suscipit ligula. Cras ac nibh sapien. Integer egestas massa ut nulla scelerisque fermentum. Donec sem nisi, laoreet id imperdiet in, porttitor ut mauris. Aliquam rutrum, dolor eu laoreet facilisis, urna felis consectetur ex, vitae blandit ex enim at magna. Proin ac suscipit turpis. Aliquam erat volutpat. Duis ac gravida velit, consectetur luctus odio. Aliquam euismod pretium felis ac commodo. Cras fermentum, libero vitae sagittis pharetra, risus nisl tincidunt tortor, et pretium odio ligula sed nisl. Nunc pellentesque tortor at ipsum convallis, ut tincidunt ligula condimentum. Donec elementum iaculis libero at feugiat. Fusce nibh ante, venenatis vitae dui vel, luctus luctus orci. Aliquam et pretium augue, sit amet scelerisque velit.',
      'Integer eu lacus non enim finibus accumsan. Sed accumsan lectus non libero pharetra, nec tristique diam dignissim. Cras hendrerit blandit odio, at sagittis arcu vehicula eu. Phasellus in neque orci. Vestibulum gravida eu nisl vitae dictum. Nam consectetur massa libero, eget consequat dui cursus nec. Donec tincidunt sit amet mauris et condimentum. Nam posuere accumsan commodo. Suspendisse tempus ultricies nisi vitae ornare. In luctus tempus rhoncus. Sed elementum vitae mauris nec efficitur. Nulla imperdiet ex eget odio rhoncus, eget pretium nunc porttitor. Nullam vehicula et nunc vitae lacinia. Praesent tempor mauris sagittis laoreet venenatis. Fusce a erat luctus, faucibus odio quis, varius urna.',
      'Ut sed congue nibh. Aenean malesuada orci erat, in tempor purus cursus et. Ut at rhoncus dolor. Donec lobortis tristique magna id scelerisque. Sed libero nunc, luctus et orci et, lobortis ultricies nibh. Nullam elementum non odio sit amet sagittis. Pellentesque sollicitudin velit justo. Vivamus volutpat sagittis ornare.',
      'Vivamus ac diam eros. In varius elit non ipsum fermentum, et pellentesque arcu suscipit. In accumsan lectus a lacus sagittis accumsan. Nulla tempor, risus et dignissim interdum, sapien ligula luctus eros, a maximus orci lorem eu nunc. Integer dapibus pretium sagittis. In sagittis tempus odio, vel consectetur dolor imperdiet sed. Pellentesque ac vestibulum nibh. Phasellus urna lectus, lobortis ut blandit sit amet, porttitor et turpis.',
      'Fusce consectetur iaculis feugiat. Morbi at nibh quis massa tincidunt gravida. Nam sagittis, nisl ut pulvinar sodales, sem elit sollicitudin quam, et rutrum orci metus a elit. Quisque ornare pulvinar mauris, vitae aliquet metus dignissim eget. In hac habitasse platea dictumst. Phasellus at dolor suscipit, dictum sapien non, consequat diam. Praesent quis congue tellus, a facilisis eros. Phasellus efficitur, ipsum eu fermentum placerat, elit lacus malesuada magna, a sagittis quam lectus a mauris.',
      'Donec non sapien sit amet tellus blandit pharetra at sed urna. Nulla nulla neque, varius eu luctus convallis, iaculis non magna. Integer eleifend, orci ut tristique pellentesque, libero felis tempor sem, tincidunt ultrices sem nisi in sem. Nullam eu sodales nisi. Vestibulum blandit felis in tortor consequat tincidunt. Mauris gravida eget nunc malesuada rhoncus. Suspendisse ut volutpat tortor, sit amet rhoncus leo. Morbi vel lorem vehicula, dapibus felis ut, fermentum nunc. Curabitur sollicitudin malesuada efficitur. Curabitur eget tellus eu nibh sollicitudin efficitur ornare in dolor. Duis congue eget purus eget ultricies. Ut ultrices turpis vitae eros volutpat tempor. Praesent et odio ipsum. Suspendisse aliquet ipsum a sollicitudin commodo. Aliquam at dignissim quam, sit amet feugiat lectus. Aliquam vitae elit eu ante consectetur vehicula eu eget tortor.',
      'Vestibulum odio odio, posuere at est eu, euismod aliquet ipsum. Nam et ex id erat dignissim luctus sed nec magna. Aenean tincidunt aliquam dictum. Morbi viverra sem dolor, sed sodales ex volutpat vel. Donec accumsan justo bibendum vestibulum consectetur. Aliquam sapien erat, consectetur eu tempor ac, imperdiet nec leo. Cras fringilla purus a odio convallis, nec elementum odio posuere. Donec luctus molestie sollicitudin. Sed vitae dictum sem. Maecenas felis tortor, condimentum sed auctor eget, sagittis quis ipsum. Sed quis purus eu eros venenatis vestibulum varius eget nulla. Interdum et malesuada fames ac ante ipsum primis in faucibus. Cras vel vestibulum diam. Nam a nisi ac turpis sodales bibendum. Vestibulum nunc dui, ultrices ut quam at, scelerisque semper est.',
      'Maecenas viverra purus ut congue fringilla. Sed eleifend sapien ac erat pellentesque lacinia. Interdum et malesuada fames ac ante ipsum primis in faucibus. Etiam auctor, est non rutrum faucibus, libero erat semper arcu, vulputate vehicula nisl turpis a arcu. Morbi ut augue luctus, eleifend erat a, euismod sem. Pellentesque tempus tempor orci eget egestas. Curabitur convallis massa sollicitudin turpis ornare convallis. Mauris venenatis elementum dui vel laoreet. In rutrum cursus sapien, nec pretium elit tempor sit amet. Morbi non neque nec nunc pulvinar aliquam. Vivamus eget odio eget sem vestibulum suscipit sit amet ut massa. Nullam aliquet dictum enim euismod luctus. Vestibulum ac fringilla est, in vestibulum enim. Quisque justo nisl, efficitur quis risus id, accumsan hendrerit ex. Vestibulum egestas arcu lacus, sit amet sagittis turpis dignissim in.',
      'Nulla facilisi. Vivamus dictum lacus in augue elementum, id elementum augue tempus. Sed sit amet dolor felis. Mauris dapibus magna nec elit rhoncus, et imperdiet erat finibus. Nulla in sagittis odio. Nunc tincidunt lobortis sapien nec vestibulum. Quisque nec fringilla diam.',
      'Aliquam laoreet congue fringilla. Mauris ullamcorper, elit id tincidunt porta, odio augue iaculis tortor, id bibendum nibh urna sed arcu. Morbi semper sem neque, id bibendum ipsum volutpat at. Vivamus tempor ligula sit amet dapibus tristique. Mauris vel hendrerit tortor. Nunc non ex ultricies elit luctus gravida id nec enim. Praesent ornare nunc eget leo interdum semper. Donec vel enim consectetur quam rutrum luctus et sed neque. Praesent sollicitudin odio eu augue pharetra consequat. Nunc viverra sapien non dignissim dignissim. Mauris vestibulum, lorem eu iaculis euismod, lectus tellus rhoncus justo, eu viverra lectus est sed arcu. Donec eget augue non purus sollicitudin porttitor. Curabitur lacinia turpis eget sem tincidunt vulputate. Pellentesque sit amet porta ligula. Aenean et ante eleifend, tincidunt lectus nec, tincidunt massa.',
      'Proin porta consequat nisl, sit amet euismod urna lobortis non. Vivamus commodo purus sit amet ex porta, id mattis nulla auctor. Integer quis nibh scelerisque erat gravida pretium. Nam accumsan eget dui et lacinia. Cras tempus tincidunt mauris in finibus. Vivamus congue ultricies lacinia. Curabitur tempor, nisi sit amet porta euismod, sem augue sollicitudin neque, sed rutrum nunc ante cursus justo. Vestibulum ac dapibus nibh. Suspendisse eu neque dolor. Praesent at imperdiet lectus, ut euismod sem.',
      'Vestibulum pretium, ante in commodo efficitur, nulla ipsum pulvinar tortor, maximus euismod augue ante nec tortor. Duis nulla lectus, placerat vel risus vel, aliquet luctus velit. Donec efficitur aliquet dui at ornare. Nam eu tortor pharetra, molestie arcu vel, rutrum tortor. Integer pulvinar nisl in libero luctus, ut luctus tortor ultrices. Praesent ullamcorper lacus velit, finibus lacinia lorem feugiat et. Etiam auctor quam at quam sagittis, id congue neque lobortis. Sed dapibus non risus eu finibus. Quisque gravida erat a est venenatis egestas. Donec elementum diam mauris, dictum tempor erat viverra sit amet.',
      'Praesent leo metus, mollis et lacinia sed, bibendum a augue. Donec ornare lacus felis, eget aliquam nisi dictum in. Phasellus dui eros, malesuada eget lorem non, blandit semper lectus. Proin ut metus in lectus tempus mollis ut eget turpis. Phasellus convallis magna vel ultricies lobortis. Curabitur est lacus, tempor id nisl at, tempor sodales tortor. Curabitur ut sollicitudin mauris. Sed et quam volutpat, tincidunt sem sed, laoreet tellus. Vivamus dictum dui dui, at pharetra sapien convallis quis. Nulla ullamcorper augue non rhoncus tempor. Cras eget diam posuere, facilisis est ac, blandit ligula. Vestibulum eu ex a quam rhoncus consectetur cursus vel odio. Integer eu lorem quis ipsum sollicitudin placerat ac non justo. Phasellus sodales sapien vel enim eleifend elementum.',
      'Vivamus sed enim ac lectus hendrerit fringilla ut ac est. Donec maximus velit sit amet velit bibendum, eu ultrices dui aliquam. Pellentesque convallis rhoncus mollis. Ut convallis maximus augue in dignissim. Nunc molestie tempus porttitor. Suspendisse potenti. Nulla tincidunt lorem dui, sed commodo felis feugiat mollis.',
      'Maecenas nec ex hendrerit, ullamcorper velit sed, sollicitudin odio. Etiam imperdiet pellentesque ex, varius convallis ligula volutpat non. Proin sodales lorem sapien, at iaculis leo venenatis at. Quisque efficitur, justo sed ultrices egestas, magna diam lobortis magna, et pulvinar lectus nisl quis turpis. Aenean tempus fringilla pellentesque. Maecenas lorem mauris, convallis eu ante a, feugiat mattis lorem. Maecenas condimentum lacus vitae tellus porta sodales. Nunc laoreet felis vitae elementum egestas.',
      'Mauris finibus faucibus lorem id aliquam. Sed porta sapien eget elit volutpat eleifend. Integer ut turpis vel quam placerat posuere. Donec dictum gravida nisl a lacinia. Nullam vel imperdiet lacus. Donec suscipit, quam sed dapibus blandit, risus tortor semper leo, nec iaculis neque lorem sed leo. Aliquam gravida ipsum quis velit dapibus consequat. Vestibulum id rhoncus turpis. Fusce ligula leo, dictum eu velit eget, vulputate condimentum dolor.',
      'Vivamus egestas ligula non rutrum sodales. In rutrum ut lorem pharetra fringilla. Donec id lorem dui. Praesent tristique justo erat, vel aliquam justo rhoncus eget. Vestibulum sed viverra eros. Maecenas eu arcu feugiat, porttitor metus et, posuere leo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum dignissim erat felis, id efficitur libero cursus a. Pellentesque cursus sodales magna et sodales. Nunc laoreet tristique magna, nec aliquet lorem pellentesque maximus.',
      'Cras varius ligula ac ipsum vehicula mattis. Duis quis velit vel sapien elementum suscipit. Integer ultrices non erat porta dapibus. Pellentesque rhoncus nisl sed odio cursus mattis. Donec sed varius arcu. Cras vel ipsum leo. In nec tellus a enim volutpat condimentum vitae id magna. Cras varius mi nisl, et blandit leo dictum sit amet. In tincidunt, eros ut molestie sagittis, massa lacus iaculis orci, vel sodales velit mauris eu nulla. Proin vel tortor porta, pellentesque justo sit amet, varius nisl.',
      'In sit amet sollicitudin ante. Morbi sit amet facilisis mauris, non fringilla eros. Maecenas elementum accumsan semper. Duis elit ante, commodo et sapien sit amet, porta eleifend lectus. Ut neque odio, aliquet quis luctus eu, congue sit amet velit. Vivamus posuere varius ligula, dapibus aliquet erat suscipit eu. Nullam volutpat justo sem, ut sodales dui venenatis sit amet. Nam ut convallis nisl, vitae elementum mi. Quisque finibus nec massa ornare fermentum. Donec quis nibh dolor. Curabitur fermentum tristique nisl. Donec justo ligula, pretium id justo eu, pharetra consequat augue. Pellentesque iaculis ut purus eget efficitur. Ut pretium nibh mollis quam dictum hendrerit. Etiam dui nibh, lacinia nec imperdiet a, volutpat quis sem.',
      'Nam facilisis est posuere ultrices mollis. Sed lacinia dapibus turpis, vel posuere erat sagittis luctus. Nunc sed ante vitae lectus gravida sollicitudin. Aliquam quis finibus libero. Aenean quis varius ligula. Ut id vehicula neque. Mauris bibendum metus non sapien volutpat, sit amet rhoncus ex venenatis. In diam nulla, condimentum eu eleifend sed, scelerisque vitae libero. Duis consectetur convallis venenatis. Proin eget lectus et enim bibendum porttitor. In sagittis sapien in purus placerat rhoncus et non nulla. Pellentesque erat mauris, elementum nec congue imperdiet, maximus sed augue. Donec maximus molestie mauris ornare pulvinar. Cras ultricies est eget nibh molestie interdum. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Phasellus id suscipit sapien, eget congue nulla.',
      'Sed ornare, felis et sagittis gravida, ante nisl pharetra elit, sed condimentum ligula metus vitae enim. Cras tempor tempus purus, ut imperdiet lectus lacinia ut. Nam ullamcorper id orci eget malesuada. Donec a vulputate odio. Quisque vel lobortis mi. Aenean facilisis mauris neque, eu ullamcorper justo pellentesque eget. Maecenas dapibus purus ac consectetur bibendum. Donec tempor, augue vel ultricies pretium, massa justo iaculis elit, in sagittis magna erat et lectus.',
      'Nullam pellentesque nulla turpis. Fusce elementum rhoncus urna, in scelerisque sapien dignissim sed. Curabitur interdum a lacus vitae mollis. Duis mollis sapien sit amet erat fermentum tincidunt. Nullam maximus libero non nunc laoreet malesuada. Duis venenatis turpis lectus, in lobortis ante euismod eget. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris ultrices venenatis neque tristique scelerisque. Quisque laoreet, orci et consectetur rutrum, est metus egestas est, vel aliquam lectus nulla ac nibh. Maecenas non ultricies arcu. Suspendisse sed orci quis sapien facilisis tristique. Etiam eleifend augue ex. Vestibulum facilisis nisi commodo urna pellentesque, vel efficitur enim mattis. Fusce faucibus est ultricies accumsan malesuada. Integer laoreet nisi ante, non fermentum ex eleifend ac.',
      'Quisque varius arcu quis dolor dictum ornare quis vulputate felis. Proin bibendum vehicula ipsum at condimentum. Morbi convallis a odio a efficitur. Praesent tortor tellus, luctus ut nisl ut, venenatis tincidunt nisl. Sed nunc ex, mollis in risus sed, fermentum tincidunt urna. Vivamus ut ornare sapien, ac sodales elit. Phasellus feugiat metus quis turpis semper vulputate. Duis condimentum diam nec turpis maximus, in fringilla quam efficitur. Integer nec luctus diam. Curabitur pellentesque eget elit et volutpat. Vivamus vel leo non mi mollis iaculis a eget arcu. Cras vitae arcu nec erat venenatis aliquam non sed nisi.',
      'Quisque a semper dui, eu malesuada purus. Nulla eget porttitor nulla. Sed a justo elementum, scelerisque dui non, vulputate nisi. Donec sed sem congue, consectetur ipsum sed, laoreet enim. Etiam nec maximus felis, quis iaculis quam. Proin eros quam, pellentesque non magna sit amet, consequat ultrices lorem. Nam ornare tincidunt facilisis. Nulla aliquam, ipsum non pharetra semper, felis nulla pulvinar erat, sit amet porta ex risus at ex. In laoreet vulputate venenatis. Phasellus gravida, nisi vel gravida pretium, tortor lectus pretium ipsum, sit amet pellentesque erat mi quis dolor. Ut maximus faucibus nunc, sit amet pretium ante consectetur eu.',
    ];
  }

}
