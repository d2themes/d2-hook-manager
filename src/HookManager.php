<?php

namespace D2\Theme;

class HookManager extends Component
{
    const ADD = 'add';
    const REMOVE = 'remove';

    protected array $defaults = [];

    public function init()
    {
        if (array_key_exists(self::ADD, $this->config) || array_key_exists(self::REMOVE, $this->config)) {
            $this->defaults = [
                Hook::TAG => false,
                Hook::CALLBACK => false,
                Hook::PRIORITY => 10,
                Hook::ARGS => 1,
                Hook::CONDITIONAL => function () {
                    return true;
                },
            ];

            $this->apply_hooks();
        }
    }

    protected function apply_hooks()
    {
        add_action('wp', function () {
            foreach ($this->config as $add_or_remove => $sub_configs) {
                foreach ($sub_configs as $sub_config => $hook) {
                    $action = $add_or_remove . '_filter';
                    $tag = $this->get_value(Hook::TAG, $hook);
                    $callback = $this->get_value(Hook::CALLBACK, $hook);
                    $priority = $this->get_value(Hook::PRIORITY, $hook);
                    $args = $this->get_value(Hook::ARGS, $hook);
                    $conditional = $this->get_value(Hook::CONDITIONAL, $hook);

                    if (is_callable($conditional) && $conditional()) {
                        $action($tag, $callback, $priority, $args);
                    }
                }
            }
        });
    }

    protected function get_value($key, $array)
    {
        return array_key_exists($key, $array) ? $array[$key] : $this->defaults[$key];
    }
}
