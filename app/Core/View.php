<?php
namespace App\Core;

class View {
    private string $layout = 'layout';
    private array $data = [];

    public function render(string $view, array $data = []): string {
        $this->data = array_merge($this->data, $data);
        $content = $this->renderOnlyView($view, $this->data);
        
        if ($this->layout === null) {
            return $content;
        }

        return $this->renderOnlyView("layouts/{$this->layout}", array_merge($this->data, ['content' => $content]));
    }

    protected function renderOnlyView(string $view, array $data): string {
        extract($data);
        ob_start();
        include __DIR__ . "/../../views/{$view}.php";
        return ob_get_clean();
    }

    public function setLayout(string $layout): void {
        $this->layout = $layout;
    }

    public function share(string $key, $value): void {
        $this->data[$key] = $value;
    }
}
