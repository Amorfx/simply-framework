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
     * @var string
     */
    public static $itsTag;

    /**
     * Condition for amp official plugin
     * @var boolean
     */
    protected $isAmp;

    /**
     * Condition for Instant Article official plugin
     * @var boolean
     */
    protected $isInstantArticle;

    /**
     * Add the shortcode to wordpress
     * CustomShortcode constructor.
     */
    public function register()
    {
        add_shortcode($this->getTag(), array($this, 'actionShortcode'));
    }

    /**
     * Get shortcode Tag
     * @return mixed
     */
    public function getTag()
    {
        return $this::$itsTag;
    }

    /**
     * Mix atts of shortcode and default parameters and finally render the shortcode
     * @param $atts
     * @param null $content
     * @return mixed
     */
    public function actionShortcode($atts, $content = null)
    {
        $atts = shortcode_atts($this->getDefaultParams(), $atts);

        $this->isAmp = function_exists('is_amp_endpoint') && is_amp_endpoint();
        $this->isInstantArticle = function_exists('is_transforming_instant_article') && is_transforming_instant_article();

        return $this->render($atts, $content);
    }

    /**
     * Return array of default params
     * @return array
     */
    abstract public function getDefaultParams();

    abstract protected function render($atts, $content);

    /**
     * Generate the shortcode string
     * @return string
     */
    public function createDefaultShortcodeString()
    {
        return $this::__createShortcodeString($this::$itsTag, $this->getDefaultParams(), '');
    }

    /**
     * Generate shortcode string
     * @param string $tag
     * @param array $params
     * @param string $content
     * @return string
     */
    protected static function __createShortcodeString($tag, $params, $content, $haveContent = true)
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

    public function createShortcodeString($params, $content, $haveContent)
    {
        return $this::__createShortcodeString($this::$itsTag, $params, $content, $haveContent);
    }
}
