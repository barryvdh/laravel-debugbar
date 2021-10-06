<?php

namespace Barryvdh\Debugbar\DataCollector;

use Illuminate\Support\Str;
use DebugBar\DataCollector\MessagesCollector as DebugBarMessagesCollector;

class MessagesCollector extends DebugBarMessagesCollector
{
    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     *
     * @param mixed $message
     * @param string $label
     */
    public function addMessage($message, $label = 'info', $isString = true)
    {
        $messageText = $message;
        $messageHtml = null;

        if (!is_string($message)) {
            // Send both text and HTML representations; the text version is used for searches
            $messageText = $this->getDataFormatter()->formatVar($message);
            if ($this->isHtmlVarDumperUsed()) {
                $messageHtml = $this->getVarDumper()->renderVar($message);
            }
            $isString = false;
        }

        $stacktrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
        $calledFromStackItem = array_pop($stacktrace);

        $stackFile = Str::of($calledFromStackItem['file']);
        // when called from views, resolve to non-cached versions of the same
        $calledFromFile = $stackFile->contains('storage/framework/views')
            ? Str::of(
                debugbar()->getCollector('views')->collect()['templates'][0]['name']
            )->match('/\(([\w\.\/]+)/')
            : $stackFile;

        $calledFileBasePath = str_replace(base_path() . '/', '', $calledFromFile);
        $calledFromLine = $calledFromStackItem['line'];

        if ($label === 'error') {
            $isString = true;
        }

        $this->messages[] = array(
            'file_name' => $calledFromFile->basename(),
            'file_path' => $calledFromFile->remove(base_path() . '/'),
            'file_line' => $calledFromLine,
            'message' => $messageText,
            'message_html' => $messageHtml,
            'is_string' => $isString,
            'label' => $label,
            'time' => microtime(true)
        );
    }
}
