<?php

namespace Vedmant\LaravelShortcodes;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Validation\ValidationException;
use Throwable;

class Renderer
{
    use Macroable;

    /**
     * @var Application Application
     */
    public $app;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var array Registered shortcodes
     */
    public $shortcodes = [];

    /**
     * @var array List of shortcodes that was rendered during the session
     */
    public $rendered = [];

    /**
     * Shortcodes renderer constructor.
     *
     * @param Application $app
     * @param Manager     $manager
     */
    public function __construct(Application $app, Manager $manager)
    {
        $this->app = $app;
        $this->manager = $manager;
    }

    /**
     * Apply shortcodes to content.
     *
     * @param string $content
     * @return string
     */
    public function apply($content)
    {
        return $this->doShortcode($content);
    }

    /**
     * Search content for shortcodes and filter shortcodes through their hooks.
     *
     * If there are no shortcode tags defined, then the content will be returned
     * without any filtering. This might cause issues when plugins are disabled but
     * the shortcode will still show up in the post or content.
     *
     * @param string $content Content to search for shortcodes.
     * @return string Content with shortcodes filtered out.
     */
    private function doShortcode($content)
    {
        if (false === strpos($content, '[')) {
            return $content;
        }

        if (empty($this->shortcodes) || ! is_array($this->shortcodes)) {
            return $content;
        }

        // Find all registered tag names in $content.
        preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches);
        $tagnames = array_intersect(array_keys($this->shortcodes), $matches[1]);

        if (empty($tagnames)) {
            return $content;
        }

        $pattern = $this->getShortcodeRegex($tagnames);
        $content = preg_replace_callback("/$pattern/", [$this, 'doShortcodeTag'], $content);

        // Always restore square braces so we don't break things like <!--[if IE ]>
        $content = $this->unescapeInvalidShortcodes($content);

        return $content;
    }

    /**
     * Regular Expression callable for do_shortcode() for calling shortcode hook.
     *
     * @param array $m Regular expression match array
     * @return false|string False on failure.
     * @throws Exception
     * @see    getShortcodeRegex for details of the match array contents.
     *
     * @global      array self::$shortcode_tags
     */
    private function doShortcodeTag($m)
    {
        $startTime = microtime(true);
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $atts = $this->shortcodeParseAtts($m[3]);
        /* @var Shortcode $shortcode */
        $shortcode = $this->shortcodes[$tag];
        $instance = null;

        if (is_callable($shortcode)) {
            $content = $m[1] . $shortcode($atts, isset($m[5]) ? $m[5] : null, $tag, $this->manager) . $m[6];
        } elseif (class_exists($shortcode)) {
            $instance = new $shortcode($this->app, $this->manager, (array) $atts, $tag);
            if (! $instance instanceof Shortcode) {
                $content = "Class {$shortcode} is not an instance of " . Shortcode::class;
            } else {
                $content = $m[1] . $this->renderShortcode($instance, isset($m[5]) ? $m[5] : null) . $m[6];
            }
        } else {
            $content = "Class {$shortcode} doesn't exists";
        }

        $this->shortcodeDone($tag, $instance, microtime(true) - $startTime);

        return $content;
    }

    /**
     * Render shortcode from the class instance
     *
     * @param Shortcode   $shortcode
     * @param string|null $content
     * @return string
     */
    private function renderShortcode(Shortcode $shortcode, $content)
    {
        try {
            return $shortcode->render($content);
        } catch (ValidationException $e) {
            return 'Validation error: <br>' . implode('<br>', Arr::flatten($e->errors()));
        } catch (Throwable $e) {
            if ($this->manager->config['throw_exceptions']) {
                throw $e;
            }

            Log::error($e);
            // Report to sentry if it's intergated
            if (class_exists('Sentry')) {
                if (app()->environment('production')) {
                    \Sentry::captureException($e);
                }
            }

            return "[$shortcode->tag] " . get_class($e) . ' ' . $e->getMessage();
        }
    }

    /**
     * Record rendered shortcode info.
     *
     * @param string             $tag
     * @param Shortcode|callable $shortcode
     * @param float              $time
     */
    private function shortcodeDone($tag, $shortcode, $time)
    {
        $this->rendered[] = $tag;

        if ($this->app->bound('debugbar')) {
            $this->app['debugbar']
                ->getCollector('shortcodes')
                ->addShortcode(compact('tag', 'shortcode', 'time'));
        }
    }

    /**
     * Retrieve the shortcode regular expression for searching.
     *
     * The regular expression combines the shortcode tags in the regular expression
     * in a regex class.
     *
     * The regular expression contains 6 different sub matches to help with parsing.
     *
     * 1 - An extra [ to allow for escaping shortcodes with double [[]]
     * 2 - The shortcode name
     * 3 - The shortcode argument list
     * 4 - The self closing /
     * 5 - The content of a shortcode when it wraps some content.
     * 6 - An extra ] to allow for escaping shortcodes with double [[]]
     *
     * @param array $tagnames List of shortcodes to find. Optional. Defaults to all registered shortcodes.
     * @return string The shortcode search regular expression
     */
    private function getShortcodeRegex($tagnames = null)
    {
        if (empty($tagnames)) {
            $tagnames = array_keys($this->shortcodes);
        }
        $tagregexp = join('|', array_map('preg_quote', $tagnames));

        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.
        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    /**
     * Retrieve the shortcode attributes regex.
     *
     * @return string The shortcode attribute regular expression
     */
    private function getShortcodeAttsRegex()
    {
        return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    }

    /**
     * Retrieve all attributes from the shortcodes tag.
     *
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     *
     * @param string $text
     * @return array|string List of attribute values.
     *                      Returns empty array if trim( $text ) == '""'.
     *                      Returns empty string if trim( $text ) == ''.
     *                      All other matches are checked for not empty().
     */
    private function shortcodeParseAtts($text)
    {
        $atts = [];
        $pattern = $this->getShortcodeAttsRegex();
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (! empty($m[1])) {
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (! empty($m[3])) {
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (! empty($m[5])) {
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) && strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } elseif (isset($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                }
            }

            // Reject any unclosed HTML elements
            foreach ($atts as &$value) {
                if (false !== strpos($value, '<')) {
                    if (1 !== preg_match('/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value)) {
                        $value = '';
                    }
                }
            }
        } else {
            $atts = ltrim($text);
        }

        return $atts;
    }

    /**
     * Remove placeholders added by do_shortcodes_in_html_tags().
     *
     * @param string $content Content to search for placeholders.
     * @return string Content with placeholders removed.
     */
    private function unescapeInvalidShortcodes($content)
    {
        // Clean up entire string, avoids re-parsing HTML.
        $trans = ['&#91;' => '[', '&#93;' => ']'];
        $content = strtr($content, $trans);

        return $content;
    }
}
