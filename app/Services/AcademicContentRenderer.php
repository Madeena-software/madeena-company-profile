<?php

namespace App\Services;

class AcademicContentRenderer
{
    private int $figureCount = 0;

    private int $tableCount = 0;

    private int $equationCount = 0;

    private array $sectionCounters = [0, 0, 0]; // H2, H3, H4

    private string $language;

    private bool $enableAutoNumbering;

    public function __construct(string $language = 'id', bool $enableAutoNumbering = true)
    {
        $this->language = $language;
        $this->enableAutoNumbering = $enableAutoNumbering;
    }

    public function render(?array $tiptapJson): string
    {
        if (! $tiptapJson || ! isset($tiptapJson['content'])) {
            return '';
        }

        $html = '';
        foreach ($tiptapJson['content'] as $node) {
            $html .= $this->renderNode($node);
        }

        return $this->processReferences($html);
    }

    private function renderNode(array $node): string
    {
        $type = $node['type'] ?? 'text';

        // Check for filament custom block
        if (isset($node['attrs']['type']) && in_array($node['attrs']['type'], ['academic-figure', 'academic-equation', 'academic-references'])) {
            $type = $node['attrs']['type'];
        }

        $html = '';

        switch ($type) {
            case 'paragraph':
                $html .= '<p>'.$this->renderChildren($node).'</p>';
                break;
            case 'heading':
                $level = $node['attrs']['level'] ?? 2;
                $text = $this->renderChildren($node);
                $number = '';
                if ($this->enableAutoNumbering && $level >= 2 && $level <= 4) {
                    $number = $this->numberSection($level).' ';
                }
                $html .= "<h{$level}>{$number}{$text}</h{$level}>";
                break;
            case 'text':
                $text = htmlspecialchars($node['text'] ?? '');
                if (isset($node['marks'])) {
                    foreach ($node['marks'] as $mark) {
                        $markType = $mark['type'];
                        if ($markType === 'bold') {
                            $text = "<strong>{$text}</strong>";
                        }
                        if ($markType === 'italic') {
                            $text = "<em>{$text}</em>";
                        }
                        if ($markType === 'underline') {
                            $text = "<u>{$text}</u>";
                        }
                        if ($markType === 'superscript') {
                            $text = "<sup>{$text}</sup>";
                        }
                        if ($markType === 'subscript') {
                            $text = "<sub>{$text}</sub>";
                        }
                        if ($markType === 'link') {
                            $href = $mark['attrs']['href'] ?? '#';
                            $target = $mark['attrs']['target'] ?? '_blank';
                            $text = "<a href=\"{$href}\" target=\"{$target}\">{$text}</a>";
                        }
                    }
                }
                $html .= $text;
                break;
            case 'bulletList':
                $html .= '<ul>'.$this->renderChildren($node).'</ul>';
                break;
            case 'orderedList':
                $html .= '<ol>'.$this->renderChildren($node).'</ol>';
                break;
            case 'listItem':
                $html .= '<li>'.$this->renderChildren($node).'</li>';
                break;
            case 'blockquote':
                $html .= '<blockquote>'.$this->renderChildren($node).'</blockquote>';
                break;
            case 'table':
                $this->tableCount++;
                $captionPrefix = $this->language === 'en' ? 'Table' : 'Tabel';
                $html .= '<div class="academic-table">';
                $html .= '<p class="table-caption">'.$captionPrefix.' '.$this->tableCount.'</p>';
                $html .= '<table>'.$this->renderChildren($node).'</table>';
                $html .= '</div>';
                break;
            case 'tableRow':
                $html .= '<tr>'.$this->renderChildren($node).'</tr>';
                break;
            case 'tableHeader':
                $html .= '<th>'.$this->renderChildren($node).'</th>';
                break;
            case 'tableCell':
                $html .= '<td>'.$this->renderChildren($node).'</td>';
                break;
            case 'academic-figure':
            case 'extension-academic-figure':
                $data = $node['attrs']['data'] ?? [];
                $this->figureCount++;
                $captionPrefix = $this->language === 'en' ? 'Figure' : 'Gambar';
                $refId = ! empty($data['ref_id']) ? $data['ref_id'] : 'fig-'.$this->figureCount;

                // Construct image path properly handling array or string
                $imagePath = is_array($data['image']) ? array_values($data['image'])[0] : $data['image'];
                $imageUrl = $imagePath ? asset('storage/'.$imagePath) : '';
                $caption = $data['caption'] ?? '';

                $html .= "<figure id=\"{$refId}\" class=\"academic-figure\">";
                $html .= "<a href=\"{$imageUrl}\" target=\"_blank\"><img src=\"{$imageUrl}\" alt=\"{$caption}\" /></a>";
                $html .= "<figcaption>{$captionPrefix} {$this->figureCount}: {$caption}</figcaption>";
                $html .= '</figure>';
                break;
            case 'academic-equation':
            case 'extension-academic-equation':
                $data = $node['attrs']['data'] ?? [];
                $this->equationCount++;
                $refId = ! empty($data['ref_id']) ? $data['ref_id'] : 'eq-'.$this->equationCount;
                $latex = htmlspecialchars($data['latex'] ?? '');

                $html .= "<div id=\"{$refId}\" class=\"academic-equation\">";
                $html .= "<span class=\"equation-content\" data-latex=\"{$latex}\"></span>";
                $html .= "<span class=\"equation-number\">({$this->equationCount})</span>";
                $html .= '</div>';
                break;
            case 'academic-references':
            case 'extension-academic-references':
                $data = $node['attrs']['data'] ?? [];
                $references = $data['references'] ?? [];

                if (empty($references)) {
                    break;
                }

                $title = $this->language === 'en' ? 'References' : 'Daftar Pustaka';
                $html .= '<section class="academic-references">';
                $html .= "<h2>{$title}</h2>";
                $html .= '<ol>';
                foreach ($references as $index => $ref) {
                    $num = $index + 1;
                    $refId = "ref-{$num}";
                    $html .= "<li id=\"{$refId}\">";
                    $html .= "{$ref['authors']} ({$ref['year']}). <em>{$ref['title']}</em>. {$ref['journal']}";
                    if (! empty($ref['volume'])) {
                        $html .= ", {$ref['volume']}";
                    }
                    if (! empty($ref['pages'])) {
                        $html .= ", {$ref['pages']}";
                    }
                    if (! empty($ref['doi'])) {
                        $doi = str_replace('https://doi.org/', '', $ref['doi']);
                        $html .= " <a href=\"https://doi.org/{$doi}\" target=\"_blank\">[DOI: {$doi}]</a>";
                    }
                    $html .= '</li>';
                }
                $html .= '</ol>';
                $html .= '</section>';
                break;
            default:
                $html .= $this->renderChildren($node);
                break;
        }

        return $html;
    }

    private function renderChildren(array $node): string
    {
        if (! isset($node['content'])) {
            return '';
        }

        $html = '';
        foreach ($node['content'] as $child) {
            $html .= $this->renderNode($child);
        }

        return $html;
    }

    private function numberSection(int $level): string
    {
        // level 2 -> index 0, level 3 -> index 1, level 4 -> index 2
        $index = $level - 2;

        // increment current level
        $this->sectionCounters[$index]++;

        // reset lower levels
        for ($i = $index + 1; $i < count($this->sectionCounters); $i++) {
            $this->sectionCounters[$i] = 0;
        }

        $numberParts = [];
        for ($i = 0; $i <= $index; $i++) {
            $numberParts[] = $this->sectionCounters[$i];
        }

        return implode('.', $numberParts);
    }

    private function processReferences(string $html): string
    {
        // Convert [@Fig. 1] or [@1] to links
        return preg_replace_callback('/\[@([^\]]+)\]/', function ($matches) {
            $text = $matches[1]; // e.g. "Fig. 1" or "1"
            $lower = strtolower($text);

            if (preg_match('/^(fig|gambar)\.?\s*(\d+)$/', $lower, $m)) {
                return "<a href=\"#fig-{$m[2]}\" class=\"xref\">[{$text}]</a>";
            }
            if (preg_match('/^(table|tabel)\.?\s*(\d+)$/', $lower, $m)) {
                return "<a href=\"#tbl-{$m[2]}\" class=\"xref\">[{$text}]</a>";
            }
            if (preg_match('/^(eq|persamaan)\.?\s*(\d+)$/', $lower, $m)) {
                return "<a href=\"#eq-{$m[2]}\" class=\"xref\">[{$text}]</a>";
            }
            if (is_numeric($text)) {
                return "<a href=\"#ref-{$text}\" class=\"xref\">[{$text}]</a>";
            }

            // Fallback for custom ref_id if someone types [@fig-custom-id]
            return "<a href=\"#{$text}\" class=\"xref\">[{$text}]</a>";
        }, $html);
    }
}
