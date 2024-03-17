<?php

namespace Simply\Core\Shortcode;

/**
 * Abstract Class used to add a shortcode
 * Class CustomShortcode
 * @package SimplyFramework\Shortcode
 */
abstract class AbstractShortcode
{
    /**
     * Tag of the shortcode (use in add_shortcode function)
     */
    public static string $itsTag;

    /**
     * Condition for amp official plugin
     */
    protected bool $isAmp;

    /**
     * Condition for Instant Article official plugin
     */
    protected bool $isInstantArticle;

    /**
     * Add the shortcode to wordpress
     * CustomShortcode constructor.
     */
    public function register(): void
    {
        add_shortcode($this->getTag(), array($this, 'actionShortcode'));
    }

    /**
     * Get shortcode Tag
     */
    public function getTag(): string
    {
        return $this::$itsTag;
    }

    /**
     * Mix atts of shortcode and default parameters and finally render the shortcode
     * @param array<mixed> $atts
     * @param null $content
     * @return mixed
     */
    public function actionShortcode(array $atts, $content = null): mixed
    {
        $atts = shortcode_atts($this->getDefaultParams(), $atts);

        $this->isAmp = function_exists('is_amp_endpoint') && is_amp_endpoint();
        $this->isInstantArticle = function_exists('is_transforming_instant_article') && is_transforming_instant_article();

        return $this->render($atts, $content);
    }

    /**
     * Return array of default params
     * @return array<mixed>
     */
    abstract public function getDefaultParams(): array;

    /** @phpstan-ignore-next-line a */
    abstract protected function render(array $atts, $content): mixed;

    /**
     * Generate the shortcode string
     */
    public function createDefaultShortcodeString(): string
    {
        return $this::__createShortcodeString($this::$itsTag, $this->getDefaultParams(), '');
    }

    /**
     * Generate shortcode string
     * @phpstan-ignore-next-line
     */
    protected static function __createShortcodeString(string $tag, array $params, string $content, bool $haveContent = true): string
    {
        $start = '[' . $tag;

        foreach ($params as $key => $value) {
            $start .= ' ' . $key . '="' . $value . '"';
        }

        $start .= ']';
        $end = '[/' . $tag . ']';

        if ($haveContent === true) {
            return $start . $content . $end;
        }

        return $start;
    }

    /**
     * @param array<mixed> $params
     * @param string $content
     * @param bool $haveContent
     * @return string
     */
    public function createShortcodeString(array $params, string $content, bool $haveContent): string
    {
        return $this::__createShortcodeString($this::$itsTag, $params, $content, $haveContent);
    }
}
