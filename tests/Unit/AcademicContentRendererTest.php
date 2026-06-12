<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AcademicContentRenderer;

class AcademicContentRendererTest extends TestCase
{
    public function test_renders_basic_paragraph_and_text()
    {
        $renderer = new AcademicContentRenderer();
        $json = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Hello World']
                    ]
                ]
            ]
        ];

        $html = $renderer->render($json);
        $this->assertEquals('<p>Hello World</p>', $html);
    }

    public function test_auto_numbers_headings()
    {
        $renderer = new AcademicContentRenderer('id', true);
        $json = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [['type' => 'text', 'text' => 'Introduction']]
                ],
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 3],
                    'content' => [['type' => 'text', 'text' => 'Background']]
                ],
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 2],
                    'content' => [['type' => 'text', 'text' => 'Methods']]
                ]
            ]
        ];

        $html = $renderer->render($json);
        $this->assertStringContainsString('<h2>1 Introduction</h2>', $html);
        $this->assertStringContainsString('<h3>1.1 Background</h3>', $html);
        $this->assertStringContainsString('<h2>2 Methods</h2>', $html);
    }

    public function test_parses_cross_references()
    {
        $renderer = new AcademicContentRenderer();
        $json = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'As seen in [@Fig. 1] and [@1].']
                    ]
                ]
            ]
        ];

        $html = $renderer->render($json);
        $this->assertStringContainsString('<a href="#fig-1" class="xref">[Fig. 1]</a>', $html);
        $this->assertStringContainsString('<a href="#ref-1" class="xref">[1]</a>', $html);
    }

    public function test_renders_figure_block()
    {
        $renderer = new AcademicContentRenderer('en');
        $json = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'academic-figure',
                    'attrs' => [
                        'type' => 'academic-figure',
                        'data' => [
                            'image' => 'test.jpg',
                            'caption' => 'Test Caption',
                            'ref_id' => 'fig-custom'
                        ]
                    ]
                ]
            ]
        ];

        $html = $renderer->render($json);
        $this->assertStringContainsString('id="fig-custom"', $html);
        $this->assertStringContainsString('Figure 1: Test Caption', $html);
    }
}
