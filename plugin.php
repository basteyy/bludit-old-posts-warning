<?php
declare(strict_types=1);

/**
 * Class pluginBluditOldPostsWarning
 */
class pluginBluditOldPostsWarning extends Plugin
{
    /** @var string Name of the database field for the template */
    private const TEMPLATE = 'template';
    /** @var string Name of the database field for the message */
    private const MESSAGE = 'message';
    /** @var string Name of the database field for the interval value */
    private const INTERVAL = 'interval_value';
    /** @var string Name of the database field for the interval type */
    private const INTERVAL_TYPE = 'interval_type';
    /** @var string Name of the database field for the position of the plugin */
    private const HOOK_POSITION = 'hook_position';
    /** @var string The default hook position */
    private const DEFAULT_HOOK_POSITION = 'pageBegin';
    /** @var string[] Allowed hook positions */
    private const HOOK_POSITIONS = ['pageBegin' => 'old-post-warning-before-post', 'pageEnd' => 'old-post-warning-after-post'];
    /** @var array Array of supported interval types */
    private const INTERVAL_TYPES = [
        'years' => 'years', 'months' => 'months', 'days' => 'days', 'hours' => 'hours', 'minutes' => 'minutes'
    ];
    /** @var string The default value for the interval */
    private const INTERVAL_DEFAULT = 1;
    /** @var string The default value for the interval value */
    private const INTERVAL_DEFAULT_TYPE = 'year';

    /**
     * Init the plugin
     */
    public function init()
    {
        $this->dbFields = [
            self::TEMPLATE      => $this->getTemplate(),
            self::INTERVAL      => self::INTERVAL_DEFAULT,
            self::INTERVAL_TYPE => self::INTERVAL_DEFAULT_TYPE,
            self::MESSAGE       => '',
            self::HOOK_POSITION => self::DEFAULT_HOOK_POSITION
        ];
    }

    /**
     * Returns the current hook position.
     * @return string
     */
    private function getHookPosition(): string
    {
        $cache = $this->getValue(self::HOOK_POSITION);
        return isset(self::HOOK_POSITIONS[$cache]) ? $cache : self::DEFAULT_HOOK_POSITION;
    }

    /**
     * Returns the default template
     * @return string
     */
    private function getDefaultTemplate(): string
    {
        return '<div class="alert alert-primary" role="alert">%1$s</div>';
    }

    /**
     * Returns the current template. If the template is deleted/empty, the default template will be returned instead.
     * @return string
     */
    private function getTemplate(): string
    {
        $cache = $this->getValue(self::TEMPLATE);
        return html_entity_decode($cache !== null && $cache !== '' ? $this->getValue(self::TEMPLATE) : $this->getDefaultTemplate());
    }

    /**
     * Returns the default message
     * @return string
     */
    private function getDefaultMessage(): string
    {
        global $L;
        return $L->g('old-post-warning-default');
    }

    /**
     * Returns the current message.
     * @return string
     */
    private function getMessage(): string
    {
        $cache = $this->getValue(self::MESSAGE);
        return $cache !== null && $cache !== "" ? $cache : $this->getDefaultMessage();
    }

    /**
     * Returns the current interval
     * @return int
     */
    private function getInterval(): int
    {
        return $this->getValue(self::INTERVAL) !== null && $this->getValue(self::INTERVAL) !== "" &&
        $this->getValue(self::INTERVAL) > 0 ? $this->getValue(self::INTERVAL) : self::INTERVAL_DEFAULT;
    }

    /**
     * Returns the current interval type
     * @param bool $readable
     * @return string
     */
    private function getIntervalType(bool $readable = false): string
    {

        $type = $this->getValue(self::INTERVAL_TYPE);

        if ($readable && isset(self::INTERVAL_TYPES[$type])) {
            return self::INTERVAL_TYPES[$type];
        }

        return $type !== null && $type !== "" ? $type : self::INTERVAL_DEFAULT_TYPE;
    }

    /**
     * Translate a array
     * @return array
     */
    private function translateArray(array $toTranslatedArray) : array
    {

        global $L;

        $rebuild = [];

        foreach($toTranslatedArray as $value => $label ) {
            $rebuild[$value] = $L->g($label);
        }

        return $rebuild;

    }

    /**
     * The plugin core where the magic happens ;)
     * @return string
     * @throws Exception
     */
    private function runPlugin(): string
    {
        global $page, $WHERE_AM_I;

        try {


            if ('page' === $WHERE_AM_I && $page->published()) {
                $interval = sprintf('-%s %s', $this->getInterval(), $this->getIntervalType());

                // Old post?
                $postDateTime = new DateTime($page->dateRaw());
                $limitDateTime = new DateTime($interval);

                if ($postDateTime < $limitDateTime) {
                    // its an old post!
                    return sprintf($this->getTemplate(),
                        $this->getMessage(),
                        (date_diff($postDateTime, (new DateTime('now'))))->format('%a')
                    );

                }

            }

            return '';

        } catch (Exception $e) {
            // Pretty unsure how to deal with a exception in bludit.
            // Depending on the debug-state, the error will be thrown.
            if (DEBUG_MODE) {
                throw new Exception($e);
            }
        }

    }

    /**
     * Place message at the beginng of the page.
     * @return string
     * @throws Exception
     */
    public function pageBegin(): string
    {
        return $this->getHookPosition() === 'pageBegin' ? $this->runPlugin() : '';
    }

    /**
     * Place message at the end of the page.
     * @return string
     * @throws Exception
     */
    public function pageEnd(): string
    {
        return $this->getHookPosition() === 'pageEnd' ? $this->runPlugin() : '';
    }

    /**
     * Returns the form for changing the settings fo the plugin.
     * @return string
     * @see See `form.php` for the formular.
     */
    public function form(): string
    {
        global $L;
        return (include __DIR__ . '/form.php');
    }
}