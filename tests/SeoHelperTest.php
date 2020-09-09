<?php

declare(strict_types=1);

namespace Arcanedev\SeoHelper\Tests;

use Arcanedev\SeoHelper\Contracts\SeoHelper as SeoHelperContract;

/**
 * Class     SeoHelperTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SeoHelperTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\SeoHelper\Contracts\SeoHelper */
    private $seoHelper;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        $this->seoHelper = $this->app[SeoHelperContract::class];
    }

    public function tearDown(): void
    {
        unset($this->seoHelper);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            \Arcanedev\SeoHelper\Contracts\SeoHelper::class,
            \Arcanedev\SeoHelper\Contracts\Renderable::class,
            \Arcanedev\SeoHelper\SeoHelper::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->seoHelper);
        }
    }

    /** @test */
    public function it_can_be_instantiated_with_helper(): void
    {
        $this->seoHelper = seo_helper();
        $expectations    = [
            \Arcanedev\SeoHelper\Contracts\SeoHelper::class,
            \Arcanedev\SeoHelper\Contracts\Renderable::class,
            \Arcanedev\SeoHelper\SeoHelper::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->seoHelper);
        }
    }

    /** @test */
    public function it_can_get_seo_meta(): void
    {
        $seoMeta = $this->seoHelper->meta();

        $expectations = [
            \Arcanedev\SeoHelper\Contracts\SeoMeta::class,
            \Arcanedev\SeoHelper\Contracts\Renderable::class,
            \Arcanedev\SeoHelper\SeoMeta::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $seoMeta);
        }
    }

    /** @test */
    public function it_can_get_seo_open_graph(): void
    {
        $ogs = [
            $this->seoHelper->openGraph(),
            $this->seoHelper->og(), // Alias
        ];

        $expectations = [
            \Arcanedev\SeoHelper\Contracts\SeoOpenGraph::class,
            \Arcanedev\SeoHelper\Contracts\Renderable::class,
            \Arcanedev\SeoHelper\SeoOpenGraph::class,
        ];

        foreach ($ogs as $seoOpenGraph) {
            foreach ($expectations as $expected) {
                static::assertInstanceOf($expected, $seoOpenGraph);
            }
        }
    }

    /** @test */
    public function it_can_set_and_render_title(): void
    {
        $title        = 'Hello World';
        $siteName     = 'ARCANEDEV';
        $separator    = '|';
        $expectations = [
            "<title>$title $separator $siteName</title>",
            '<meta property="og:title" content="'.$title.'">',
            '<meta property="og:site_name" content="'.$siteName.'">',
            '<meta name="twitter:title" content="'.$title.'">',
        ];

        $this->seoHelper->setTitle($title, $siteName, $separator);

        foreach ($expectations as $expected) {
            static::assertStringContainsString($expected, $this->seoHelper->render());
            static::assertStringContainsString($expected, (string) $this->seoHelper);
        }
    }

    /** @test */
    public function it_can_set_and_render_site_name(): void
    {
        $title        = 'My Application';
        $siteName     = 'ARCANEDEV';
        $expectations = [
            "<title>{$title} - {$siteName}</title>",
            '<meta property="og:title" content="'.$title.'">',
            '<meta property="og:site_name" content="'.$siteName.'">',
            '<meta name="twitter:title" content="'.$title.'">',
        ];

        $this->seoHelper->setSiteName($siteName)
                        ->setTitle($title);

        foreach ($expectations as $expected) {
            static::assertStringContainsString($expected, $this->seoHelper->render());
            static::assertStringContainsString($expected, (string) $this->seoHelper);
        }
    }

    /** @test */
    public function it_can_toggle_site_name_visibility(): void
    {
        $title    = 'My Application';
        $siteName = 'ARCANEDEV';

        $this->seoHelper->setTitle($title, $siteName);

        static::assertStringContainsString(
            "<title>{$title} - {$siteName}</title>",
            $this->seoHelper->render()
        );

        $this->seoHelper->hideSiteName();

        static::assertStringContainsString(
            "<title>{$title}</title>",
            $this->seoHelper->render()
        );

        $this->seoHelper->showSiteName();

        static::assertStringContainsString(
            "<title>{$title} - {$siteName}</title>",
            $this->seoHelper->render()
        );
    }

    /** @test */
    public function it_can_set_and_render_description(): void
    {
        $description  = 'ARCANEDEV super description';
        $expectations = [
            '<meta name="description" content="'.$description.'">',
            '<meta property="og:description" content="'.$description.'">',
            '<meta name="twitter:description" content="'.$description.'">',
        ];

        $this->seoHelper->setDescription($description);

        foreach ($expectations as $expected) {
            static::assertStringContainsString($expected, $this->seoHelper->render());
            static::assertStringContainsString($expected, (string) $this->seoHelper);
        }
    }

    /** @test */
    public function it_can_set_and_render_keywords(): void
    {
        $keywords = $this->getSeoHelperConfig('keywords.default');
        $expected = '<meta name="keywords" content="'.implode(', ', $keywords).'">';

        $this->seoHelper->setKeywords($keywords); // Array

        static::assertStringContainsString($expected, $this->seoHelper->render());
        static::assertStringContainsString($expected, (string) $this->seoHelper);

        $this->seoHelper->setKeywords(implode(',', $keywords)); // String

        static::assertStringContainsString($expected, $this->seoHelper->render());
        static::assertStringContainsString($expected, (string) $this->seoHelper);
    }

    /** @test */
    public function it_can_set_and_render_image(): void
    {
        $this->seoHelper->setImage($imageUrl = 'http://localhost/assets/img/logo.png');

        $expectations = [
            '<meta property="og:image" content="'.$imageUrl.'">',
            '<meta name="twitter:image" content="'.$imageUrl.'">'
        ];

        $rendered = $this->seoHelper->render();

        foreach ($expectations as $expected) {
            static::assertStringContainsString($expected, $rendered);
        }
    }

    /** @test */
    public function it_can_set_and_render_url(): void
    {
        $this->seoHelper->setUrl($url = 'http://localhost/path');

        $expectations = [
            '<link rel="canonical" href="'.$url.'">',
            '<meta property="og:url" content="'.$url.'">'
        ];

        $rendered = $this->seoHelper->render();

        foreach ($expectations as $expected) {
            static::assertStringContainsString($expected, $rendered);
        }
    }

    /** @test */
    public function it_can_render_all(): void
    {
        $output = $this->seoHelper->render();

        static::assertNotEmpty($output);
    }

    /** @test */
    public function it_can_render_all_with_html_string_object(): void
    {
        $output = $this->seoHelper->renderHtml();

        static::assertInstanceOf(\Illuminate\Support\HtmlString::class, $output);
        static::assertNotEmpty($output->toHtml());
    }

    /** @test */
    public function it_can_enable_and_disable_open_graph(): void
    {
        $needle = '<meta property="og:';

        static::assertTrue($this->seoHelper->openGraph()->isEnabled());
        static::assertFalse($this->seoHelper->openGraph()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->disableOpenGraph();

        static::assertFalse($this->seoHelper->openGraph()->isEnabled());
        static::assertTrue($this->seoHelper->openGraph()->isDisabled());
        static::assertStringNotContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->enableOpenGraph();

        static::assertTrue($this->seoHelper->openGraph()->isEnabled());
        static::assertFalse($this->seoHelper->openGraph()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->openGraph()->disable();

        static::assertFalse($this->seoHelper->openGraph()->isEnabled());
        static::assertTrue($this->seoHelper->openGraph()->isDisabled());
        static::assertStringNotContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->openGraph()->enable();

        static::assertTrue($this->seoHelper->openGraph()->isEnabled());
        static::assertFalse($this->seoHelper->openGraph()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());
    }

    /** @test */
    public function it_can_enable_and_disable_twitter_card(): void
    {
        $needle = '<meta name="twitter:';

        static::assertTrue($this->seoHelper->twitter()->isEnabled());
        static::assertFalse($this->seoHelper->twitter()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->disableTwitter();

        static::assertFalse($this->seoHelper->twitter()->isEnabled());
        static::assertTrue($this->seoHelper->twitter()->isDisabled());
        static::assertStringNotContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->enableTwitter();

        static::assertTrue($this->seoHelper->twitter()->isEnabled());
        static::assertFalse($this->seoHelper->twitter()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->twitter()->disable();

        static::assertFalse($this->seoHelper->twitter()->isEnabled());
        static::assertTrue($this->seoHelper->twitter()->isDisabled());
        static::assertStringNotContainsString($needle, $this->seoHelper->render());

        $this->seoHelper->twitter()->enable();

        static::assertTrue($this->seoHelper->twitter()->isEnabled());
        static::assertFalse($this->seoHelper->twitter()->isDisabled());
        static::assertStringContainsString($needle, $this->seoHelper->render());
    }
}
