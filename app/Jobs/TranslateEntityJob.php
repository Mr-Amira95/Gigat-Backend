<?php

namespace App\Jobs;

use App\Utilities\GoogleTranslator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    /**
     * @param string $modelClass  Fully-qualified Eloquent model class
     * @param int    $entityId    Primary key of the entity
     * @param string $text        Source text to translate
     * @param string $field       The column on the translations table to update (e.g. 'action', 'title')
     * @param string $relation    Name of the translation relationship on the entity (default 'translations')
     */
    public function __construct(
        private string $modelClass,
        private int    $entityId,
        private string $text,
        private string $field,
        private string $relation = 'translations'
    ) {}

    public function handle(GoogleTranslator $translator): void
    {
        $entity = ($this->modelClass)::find($this->entityId);
        if (! $entity) {
            return;
        }

        $translations = $translator->translateForStorage($this->text);

        foreach ($translations as $lang => $translatedText) {
            $entity->{$this->relation}()
                ->where('language', $lang)
                ->update([$this->field => $translatedText]);
        }
    }
}
