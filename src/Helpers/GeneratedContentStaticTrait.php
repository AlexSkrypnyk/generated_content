<?php

declare(strict_types=1);

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
   * @var string[]|null
   */
  protected static $staticContent = NULL;

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
  public static function staticSentence(int $count = 5): string {
    $content = '';
    do {
      $content .= ' ' . static::staticParagraphs();
    } while (count(explode(' ', trim($content))) < $count);

    $words = explode(' ', trim($content));
    $words = array_slice($words, 0, $count);
    $content = implode(' ', $words);

    return rtrim($content, '.') . '.';
  }

  /**
   * Generates a static string.
   */
  public static function staticString(int $length = 32): string {
    $content = '';
    do {
      $content .= preg_replace('/[^a-zA-Z0-9]/', '', static::staticParagraphs());
    } while (strlen($content) < $length);

    return strtolower(substr($content, 0, $length));
  }

  /**
   * Generates a static name.
   */
  public static function staticName(int $length = 16): string {
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
  public static function staticAbbreviation(int $length = 2): string {
    return static::staticName($length);
  }

  /**
   * Generate a pre-defined static plain-text paragraph.
   *
   * @return string
   *   Static content string.
   */
  public static function staticPlainParagraph(): string {
    $content = static::staticParagraphs();

    return trim($content);
  }

  /**
   * Generate a pre-defined static HTML paragraph.
   *
   * @return string
   *   Static content string.
   */
  public static function staticHtmlParagraph(): string {
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
  public static function staticHtmlHeading(int $words = 5, int $level = 1, string $prefix = ''): string {
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
  public static function staticRichText(int $paragraphs = 4, string $prefix = ''): string {
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
  protected static function staticParagraphs(int $paragraphs = 1, string $delimiter = "\n\n"): string {
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
   *
   * @return string[]
   *   Static content.
   */
  protected static function staticContent(): array {
    return [
      'Accusamus animi deleniti doloribus libero molestiae possimus provident quo. Assumenda blanditiis qui repudiandae rerum. Deleniti excepturi harum iusto optio praesentium. Libero minus optio provident rerum temporibus tenetur voluptatibus. Doloribus eligendi necessitatibus optio praesentium. Corrupti dolores laborum maiores molestias odio officiis saepe. Deserunt nobis repellendus. Consequatur deleniti eligendi odio. Cumque deserunt dolor impedit iusto laborum nihil provident quibusdam rerum.',
      'Earum perferendis sapiente sunt. Deleniti non provident. Excepturi harum iusto maiores. Alias doloribus est perferendis praesentium vero. Blanditiis cumque dolores earum praesentium quibusdam quo reiciendis voluptas. Corrupti deserunt dolorum ducimus praesentium quidem quod reiciendis repudiandae. Animi deleniti dolores soluta. Atque consequatur dignissimos dolores fuga impedit itaque possimus tenetur. Accusamus cupiditate deleniti eos est quod quos rerum saepe.',
      'Est iusto maxime qui repellat rerum saepe tenetur voluptas. Eligendi molestias possimus. Aut expedita itaque officia placeat quibusdam voluptatum. Accusamus deserunt impedit laborum nam nihil officia soluta voluptas. Alias earum qui vero. Atque autem debitis impedit quibusdam rerum tenetur voluptas. Consequatur dolorum iusto laborum maiores nobis quibusdam quidem vero. In non quo repudiandae.',
      'At cumque hic laborum quo repellat. Cumque delectus excepturi maxime sint. Autem blanditiis culpa cupiditate eos expedita in quod quos voluptatum. Facilis iusto libero saepe. Corrupti dignissimos distinctio facere molestiae perferendis provident similique soluta sunt. Blanditiis culpa occaecati repudiandae vero.',
      'Id impedit maxime minus nam nihil omnis optio qui sapiente. Asperiores corrupti quo ut. Blanditiis nihil voluptatum. Alias doloribus dolorum est maiores nam nihil. Atque dignissimos ducimus excepturi facilis laborum maxime nam quibusdam repudiandae. Aut dolorum eos in maiores molestias. Accusamus autem debitis in nam nobis qui ut. At aut deleniti distinctio hic maiores sint voluptates.',
      'Harum id nam quas. Debitis eos eveniet in maiores necessitatibus qui quidem tempore vero. Aut consequatur cumque deserunt eos repudiandae similique tempore. Cupiditate facilis libero perferendis quibusdam sapiente. Et laborum quas quibusdam reiciendis sint voluptatum. Aut culpa debitis earum laborum nobis odio praesentium.',
      'Asperiores atque molestias mollitia praesentium. Accusamus assumenda eligendi in iusto non optio possimus ut voluptas. Et iusto sunt vero. Accusamus cumque mollitia rerum. At cupiditate eos possimus qui quidem similique tempore vero voluptates. Consequatur eos harum qui quos similique. Deserunt dolor itaque necessitatibus voluptatum.',
      'Atque dolor itaque provident recusandae voluptates. Culpa facere voluptas. Distinctio facere sapiente. Asperiores earum fuga libero odio qui quo voluptas. Animi at id officia omnis. Alias at culpa minus molestias soluta tempore voluptatum. Aut earum soluta. Aut non placeat.',
      'Consequatur deserunt doloribus hic itaque quo quod ut. Distinctio fuga nihil officia repellat voluptatum. Culpa debitis et eveniet hic itaque minus mollitia officia possimus. Alias animi eligendi fuga impedit maiores mollitia quidem similique. Eveniet reiciendis voluptas. Doloribus hic libero officiis perferendis possimus quos voluptas. Mollitia nam quo. Distinctio in itaque libero nihil nobis recusandae repudiandae. Et maxime placeat qui repellendus.',
      'Libero nam odio officiis sapiente. Autem facere molestiae nobis omnis recusandae tempore. Asperiores dolor excepturi id maiores maxime officia placeat quos repellendus.',
      'Culpa debitis nobis. Assumenda autem facilis libero non. Dolorum itaque vero. Cupiditate dignissimos non possimus quos. Delectus facilis laborum molestiae odio omnis sunt voluptatibus. Animi cumque officia omnis possimus quidem reiciendis tenetur. Asperiores delectus impedit molestias mollitia quas quos temporibus. Aut dolores doloribus ducimus nobis non praesentium saepe sint soluta. Dolor eveniet odio quidem quo sunt temporibus voluptatibus.',
      'Aut delectus id minus molestias recusandae repudiandae. Animi deserunt optio. Atque cupiditate delectus dignissimos quas quos. Et id non voluptatibus. Expedita non occaecati odio similique. Assumenda et facilis laborum necessitatibus non qui quidem sint soluta. Molestiae recusandae rerum ut voluptatum. Cupiditate eos facilis maiores nihil odio officiis provident temporibus voluptates.',
      'Debitis delectus et in non rerum. At atque doloribus eligendi eos nobis omnis provident qui repudiandae. Asperiores doloribus excepturi id odio. Deleniti harum libero officiis. At autem deleniti dolores facere in nam similique. Debitis in iusto necessitatibus non officia quod voluptatum. Culpa cupiditate dolores officiis quas vero.',
      'Dignissimos doloribus harum nihil quos repellat saepe sapiente. Eligendi hic omnis optio praesentium quod repellat. Alias culpa deserunt dolores eveniet qui repellendus. Animi excepturi hic libero nihil qui voluptas. Animi corrupti maxime mollitia voluptatibus. Alias distinctio ducimus facere minus mollitia optio provident quidem voluptas.',
      'Dolores eos maxime temporibus. Asperiores aut doloribus dolorum id nobis tempore. Assumenda blanditiis corrupti debitis earum fuga nobis provident qui recusandae. Placeat quod tenetur. Deleniti eligendi eos sint. Culpa deserunt iusto libero officia optio praesentium tenetur. Accusamus animi deserunt fuga libero quod. Accusamus blanditiis cumque dolor id laborum placeat quibusdam similique.',
      'Eveniet occaecati recusandae. Consequatur debitis dignissimos dolorum hic mollitia quidem reiciendis rerum sunt. Accusamus aut culpa maxime necessitatibus non quas. Accusamus expedita libero maiores maxime optio saepe sunt voluptatum. Cupiditate ducimus quibusdam. Blanditiis dolor id iusto libero recusandae. Deserunt maxime necessitatibus non. Alias asperiores at omnis. Dolores doloribus excepturi libero perferendis. Cumque dolor excepturi laborum mollitia quas quo temporibus.',
      'Eos iusto optio quidem similique. Alias corrupti excepturi facilis molestiae optio quos repellat ut. Animi officiis possimus. Assumenda eveniet facere fuga itaque odio praesentium quos. Dolor doloribus earum harum praesentium quod. Dolor praesentium quibusdam tenetur. Cumque deleniti eligendi libero quod ut. Cumque cupiditate doloribus eos mollitia voluptas. Doloribus in voluptas. Alias atque consequatur corrupti dolores dolorum eveniet non similique. Accusamus atque consequatur cupiditate id odio officiis quos repellat saepe.',
      'Assumenda cupiditate mollitia quos repellendus. Alias culpa earum eligendi hic nam. Necessitatibus optio perferendis quidem. Dolores eligendi impedit itaque iusto omnis repellendus tenetur. Autem dignissimos possimus sunt. At cupiditate eligendi excepturi facere maiores molestiae officia optio. Consequatur culpa dignissimos maxime similique sunt tenetur. Animi consequatur cupiditate debitis occaecati optio placeat quibusdam quod. Consequatur distinctio est facere iusto libero sapiente voluptas voluptates.',
      'In molestiae occaecati possimus qui recusandae. Animi asperiores dolorum itaque quo temporibus. Autem impedit odio placeat qui rerum similique ut. Cumque delectus earum maiores nam quibusdam quod repudiandae soluta. Consequatur debitis et eveniet impedit nam quos soluta. Expedita molestias necessitatibus. Delectus ducimus facilis id molestias nobis vero. Cumque ducimus et excepturi hic laborum mollitia repellat. Corrupti cumque deleniti deserunt hic non.',
      'Excepturi laborum minus necessitatibus. Minus nam officiis sunt. Alias dolor molestias nobis occaecati repudiandae similique voluptates. Expedita fuga ut. Animi est in omnis optio. Deleniti dignissimos laborum maxime praesentium quas quod temporibus. Accusamus corrupti et mollitia soluta voluptatibus voluptatum. Atque cupiditate recusandae sapiente. Consequatur corrupti deleniti et minus omnis placeat quo repudiandae saepe.',
      'Ducimus eligendi nobis. Debitis eligendi est hic iusto quibusdam vero voluptatum. Aut distinctio facilis officia repudiandae sunt. Assumenda autem eligendi iusto odio repellat voluptas voluptatum. Autem corrupti est fuga officia saepe soluta. Animi aut iusto maxime nobis provident. Accusamus asperiores culpa harum libero non sapiente ut voluptates. Deserunt eligendi hic laborum quod recusandae temporibus voluptas. Aut ducimus impedit necessitatibus officia optio possimus quod similique voluptatibus.',
      'Autem consequatur expedita facere laborum placeat quibusdam. Accusamus autem id mollitia omnis placeat qui tenetur. Assumenda eos facilis reiciendis. Deserunt dolorum quidem similique. Itaque maxime officiis quidem repudiandae. At maxime non repellendus saepe. Assumenda distinctio ducimus excepturi molestiae optio voluptatibus.',
      'Et itaque provident rerum. Delectus dignissimos facere hic id laborum maiores molestias quo. Atque culpa delectus distinctio itaque quo sunt tenetur voluptates. Deserunt dolores maxime molestiae. Animi atque est excepturi fuga quas soluta. Autem dignissimos harum nam voluptas. Dolor earum eveniet. Distinctio facere iusto voluptatum. Blanditiis corrupti debitis doloribus excepturi itaque quibusdam repellat soluta tenetur.',
      'Alias aut quas ut. Assumenda consequatur dignissimos dolor et hic id occaecati possimus sunt. Autem facere id necessitatibus recusandae repudiandae. Alias assumenda blanditiis facere perferendis saepe. Aut cumque id libero repellat voluptatibus. Asperiores atque consequatur distinctio facilis fuga provident quibusdam quo voluptatibus. Eligendi eos et expedita tempore voluptatum. Maxime minus molestiae saepe. Alias cupiditate sunt vero. Consequatur cupiditate in nobis non voluptas.',
      'Doloribus iusto officia ut. Culpa doloribus iusto nam nobis odio recusandae vero. Accusamus atque culpa in occaecati quidem quo voluptas. Autem distinctio hic praesentium sint vero. Quo similique vero. Distinctio occaecati optio possimus quas saepe voluptates. Cumque delectus eos in maiores tenetur.',
      'Blanditiis deserunt fuga itaque quo reiciendis repellendus sapiente sunt. Distinctio minus repellat voluptatibus voluptatum. Corrupti distinctio dolorum earum eligendi repellat repellendus sapiente. Doloribus excepturi expedita maiores molestiae officia tenetur ut voluptas. Cupiditate dolores odio quas. Animi aut consequatur doloribus laborum sunt. Asperiores deleniti ducimus et excepturi maiores quas qui voluptates. Deleniti dignissimos excepturi mollitia voluptas.',
      'Delectus dolores eligendi hic laborum odio officia recusandae ut voluptatum. Corrupti est nobis quibusdam. Autem distinctio mollitia odio. At delectus fuga impedit maiores molestiae repellendus rerum vero. Consequatur distinctio et maxime nam non officiis placeat temporibus. Alias ducimus eligendi et in molestias quidem quod reiciendis voluptatum. Atque est non optio quos repellendus voluptatibus. Autem debitis hic officia qui quod repellendus soluta voluptatibus.',
      'Deleniti mollitia quo repudiandae vero. Deserunt doloribus itaque odio placeat possimus praesentium quod tempore. Alias cumque est maiores. Corrupti ducimus eos nobis possimus quas sint. Alias at consequatur eligendi possimus recusandae saepe. Alias blanditiis dolorum mollitia odio qui sapiente similique. Consequatur distinctio harum placeat quibusdam. Corrupti earum repudiandae rerum vero. At cupiditate delectus dolor est nam officia praesentium rerum. Consequatur deserunt dignissimos expedita repellendus.',
      'Animi aut culpa maiores. Dignissimos dolor est laborum optio provident reiciendis. Doloribus facere fuga temporibus voluptas. Deleniti earum iusto maxime. Cumque facilis nam odio officia optio placeat possimus quidem quos. Libero repellat vero voluptatibus. Id maiores sint. Dolor et expedita itaque molestias mollitia quo sunt voluptatibus. Atque distinctio hic placeat saepe voluptatibus. Deleniti distinctio dolores itaque maxime officiis placeat praesentium qui.',
      'Facilis laborum officiis quidem quod recusandae repellat repudiandae sapiente sint. Ducimus eos facilis saepe similique tempore. Animi autem corrupti et impedit minus molestiae optio. Animi cupiditate distinctio hic maxime quibusdam. Asperiores eos et maiores quas recusandae rerum. Officia tempore voluptates. Corrupti deleniti dignissimos doloribus ducimus itaque ut. Est excepturi expedita in praesentium recusandae voluptatibus.',
      'Dolorum eveniet libero necessitatibus odio officiis quo quod recusandae voluptatibus. At aut cumque fuga hic in minus mollitia necessitatibus similique. Assumenda eveniet mollitia officia placeat tempore. Accusamus asperiores atque deleniti facere quos soluta.',
      'Molestiae sapiente tempore. At maxime quas quod repellat rerum saepe similique tenetur. Animi consequatur culpa deleniti dolorum molestiae occaecati vero. At dignissimos id minus mollitia quidem repudiandae saepe soluta voluptas. Facere harum molestias necessitatibus quos repellendus. Assumenda cumque dignissimos libero non quas qui quod repellat. Alias asperiores facere libero minus nihil officiis repellendus rerum.',
      'Eligendi expedita officia. Animi dolorum nam nobis odio officia quos. Cupiditate delectus maiores perferendis similique sunt. Asperiores molestiae mollitia possimus sunt. Impedit non provident quibusdam quidem repudiandae sunt tempore. Assumenda eos est mollitia occaecati perferendis. Consequatur eos id reiciendis repellat ut.',
      'Deserunt temporibus voluptatibus voluptatum. Debitis laborum molestiae officia quibusdam reiciendis similique. At culpa deleniti possimus. Dolor est laborum molestias nobis. Atque aut officia optio placeat quibusdam repudiandae. Atque distinctio facilis maiores molestias nihil optio qui quibusdam voluptas. Atque blanditiis culpa cupiditate deserunt et eveniet iusto perferendis quos. Distinctio facere libero mollitia optio perferendis tenetur ut vero voluptates.',
      'Maiores molestiae saepe voluptas. Eos est facilis. Debitis facere itaque mollitia officiis omnis quo ut. Atque autem dignissimos excepturi impedit optio quos. Aut molestiae non optio quod. Animi delectus dolores quidem repellat repudiandae tenetur. Accusamus atque delectus et molestiae qui repellat temporibus. At dignissimos distinctio dolorum earum nobis possimus quidem recusandae ut. Excepturi laborum officia qui. Corrupti dignissimos eveniet nihil officia omnis qui reiciendis sunt tempore.',
      'Non officia officiis quibusdam vero. Autem facilis hic in necessitatibus non occaecati similique voluptas. Consequatur cupiditate deserunt impedit libero optio qui reiciendis saepe vero. At dolores facere iusto nam odio officiis perferendis repudiandae voluptatum.',
      'Facere id maxime nihil odio perferendis repellendus voluptas. Alias corrupti omnis quibusdam sint tempore. Blanditiis in maxime optio quos repellat sint temporibus tenetur voluptatibus. Animi delectus distinctio provident repellendus saepe vero. Facere fuga odio temporibus. At ducimus odio optio temporibus. Deleniti est expedita minus nihil placeat quo sint tenetur.',
      'Dolor omnis optio repudiandae. Animi deleniti eveniet. Aut eos est praesentium saepe voluptatum. Aut dignissimos eligendi excepturi molestiae molestias sint tenetur vero. Est iusto nihil quod repellendus tempore.',
      'Laborum occaecati omnis. Eligendi possimus praesentium qui. Corrupti cupiditate eos laborum possimus quibusdam sunt temporibus. Et quos similique. Atque deserunt dolor fuga minus mollitia quos reiciendis voluptatibus. Alias asperiores atque ducimus et expedita praesentium. Assumenda blanditiis cupiditate dignissimos maiores nam non recusandae repellendus voluptatum. Corrupti deleniti expedita nam nobis soluta. Assumenda dolorum et facere maxime necessitatibus quod recusandae ut.',
      'Minus molestias mollitia officiis qui. Id in molestiae omnis quidem repudiandae voluptatum. Delectus excepturi minus molestias nam placeat quos reiciendis similique soluta. Assumenda eos et excepturi maiores quod similique sint. Animi ducimus earum expedita itaque maxime molestias possimus sint voluptates.',
      'Occaecati perferendis tempore. Accusamus id praesentium quas temporibus. Consequatur expedita mollitia optio voluptas. Accusamus dolor et. Itaque necessitatibus possimus praesentium. Aut consequatur deleniti hic necessitatibus placeat rerum ut voluptas voluptatum. Aut est fuga iusto officia perferendis praesentium recusandae tenetur. Officia qui reiciendis similique sint. Animi eligendi id. At debitis dolorum eos eveniet occaecati quibusdam reiciendis voluptatibus.',
      'Expedita officia quod recusandae. Earum expedita maiores molestiae repellat. Distinctio maiores odio quas soluta tenetur. Asperiores assumenda odio optio praesentium quas repellat sint tenetur. Corrupti facilis officia. Delectus in occaecati perferendis quo tenetur.',
      'Mollitia non sunt. Animi eos impedit non occaecati officiis recusandae reiciendis voluptatibus. Asperiores impedit itaque quas repudiandae sapiente tenetur voluptas. Autem ducimus in praesentium similique. Autem earum et itaque repellat. Delectus dolorum non. Consequatur cupiditate doloribus eveniet in iusto nihil non possimus.',
      'Impedit non optio quod quos repudiandae sapiente tenetur. Blanditiis consequatur facere quas temporibus. Blanditiis minus perferendis. Impedit maxime officia. Corrupti delectus dolorum mollitia omnis placeat quos reiciendis. Aut minus quos voluptas. Debitis facilis officia rerum soluta. Alias delectus itaque iusto necessitatibus officiis sint tempore voluptatum.',
      'Corrupti id saepe. Blanditiis culpa cupiditate necessitatibus nobis repudiandae similique temporibus. Atque cupiditate eos facilis maiores recusandae reiciendis voluptates. Doloribus est expedita impedit molestias occaecati similique soluta voluptatum. Doloribus itaque odio. Debitis id quo.',
      'Cupiditate est maiores non repellendus. Dolorum itaque maiores quo. At atque consequatur facilis molestiae nobis non perferendis voluptates. Culpa est maiores maxime mollitia possimus praesentium quo tempore. Animi asperiores nihil non. Consequatur cumque necessitatibus omnis soluta tempore ut voluptates. Asperiores doloribus maxime sint tenetur. At eos est fuga harum mollitia non praesentium quas vero.',
      'Fuga id impedit necessitatibus soluta tempore. Accusamus eveniet iusto laborum placeat recusandae rerum. Cumque et reiciendis. Assumenda facilis molestiae placeat praesentium voluptas. Doloribus fuga hic maiores soluta tenetur. Accusamus eligendi facere laborum perferendis placeat quo repudiandae sapiente sunt. Deserunt est libero molestias reiciendis. Eveniet hic maiores provident ut voluptas. Eveniet occaecati praesentium quidem reiciendis vero. Doloribus iusto praesentium quos.',
      'Officia officiis quos. Facere harum omnis. Dignissimos quidem repellendus saepe sint temporibus. Distinctio occaecati odio quos. Facere reiciendis voluptas voluptatum. Animi facilis officiis soluta. Hic repellat soluta. Dolores harum non saepe tenetur. Dolorum excepturi nam occaecati. Dolor earum repellendus. Autem itaque qui. Hic itaque necessitatibus quos repudiandae voluptatum. Debitis delectus molestiae perferendis sapiente tempore.',
      'Distinctio harum praesentium quod quos repellat tempore ut voluptatum. Earum eveniet itaque vero voluptatibus. Corrupti eos facere temporibus. Deserunt eveniet id minus nihil placeat voluptatibus. Dolor earum itaque possimus tenetur vero.',
      'Cumque delectus hic mollitia necessitatibus nihil nobis repellat voluptatibus. Alias at eveniet harum molestias officiis sapiente similique. Aut debitis et molestiae occaecati quidem saepe soluta tempore. Consequatur culpa doloribus est eveniet itaque libero nam odio quos.',
    ];
  }

}
