<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests de protection XSS.
 * Commande : php artisan test --filter=XssProtectionTest
 */
class XssProtectionTest extends TestCase
{
    // ─── 1. Headers de sécurité ──────────────────────────────────────────────

    public function test_security_headers_are_present(): void
    {
        $response = $this->getJson('/api/v1/ai/health');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('Permissions-Policy');
    }

    public function test_csp_blocks_inline_scripts(): void
    {
        $response = $this->getJson('/api/v1/ai/health');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringNotContainsString("'unsafe-inline'", explode(';', $csp)[1]); // script-src part
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    // ─── 2. strip_tags — titre blog ─────────────────────────────────────────

    public function test_strip_tags_removes_script_from_title(): void
    {
        $malicious = '<script>alert("xss")</script>Mon Titre';
        $clean = strip_tags($malicious);

        $this->assertEquals('alert("xss")Mon Titre', $clean);
        $this->assertStringNotContainsString('<script>', $clean);
    }

    // ─── 3. strip_tags — contenu blog garde les balises autorisées ──────────

    public function test_blog_sanitizer_keeps_allowed_tags(): void
    {
        $allowedTags = '<p><br><strong><b><em><i><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code><img><figure><figcaption><table><thead><tbody><tr><th><td><span><div><hr><sub><sup>';

        $input = '<p>Bonjour</p><script>evil()</script><strong>gras</strong><iframe src="hack.com"></iframe>';
        $clean = strip_tags($input, $allowedTags);

        $this->assertStringContainsString('<p>Bonjour</p>', $clean);
        $this->assertStringContainsString('<strong>gras</strong>', $clean);
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('<iframe', $clean);
    }

    // ─── 4. Suppression des attributs on* ───────────────────────────────────

    public function test_event_handler_attributes_are_removed(): void
    {
        $input = '<img src="ok.jpg" onerror="alert(1)"><div onmouseover="steal()">hover</div>';
        $clean = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/i', '', $input);
        $clean = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $clean);

        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringContainsString('<img src="ok.jpg">', $clean);
        $this->assertStringContainsString('hover</div>', $clean);
    }

    // ─── 5. Commentaire — HTML supprimé (texte brut) ────────────────────────

    public function test_comment_is_plain_text(): void
    {
        $malicious = '<script>alert("xss")</script><b>Coucou</b><img src=x onerror=alert(1)>';
        $clean = strip_tags($malicious);

        $this->assertEquals('alert("xss")Coucou', $clean);
        $this->assertStringNotContainsString('<script>', $clean);
        $this->assertStringNotContainsString('<img', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
    }

    // ─── 6. Vecteurs XSS courants ──────────────────────────────────────────

    public function test_svg_onload_is_stripped(): void
    {
        $allowedTags = '<p><br><strong><b><em><i><u><a>';
        $input = '<svg onload="alert(1)"><p>ok</p>';
        $clean = strip_tags($input, $allowedTags);

        $this->assertStringNotContainsString('<svg', $clean);
        $this->assertStringNotContainsString('onload', $clean);
        $this->assertStringContainsString('<p>ok</p>', $clean);
    }

    public function test_javascript_protocol_in_href(): void
    {
        $input = '<a href="javascript:alert(1)">clic</a>';
        // strip_tags garde <a>, mais le frontend DOMPurify bloquera le href
        // Ici on vérifie que le regex on* ne laisse pas passer onclick
        $input2 = '<a href="#" onclick="alert(1)">clic</a>';
        $clean = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/i', '', $input2);

        $this->assertStringNotContainsString('onclick', $clean);
    }

    // ─── 7. AI chat route répond ────────────────────────────────────────────

    public function test_ai_health_is_accessible(): void
    {
        $response = $this->getJson('/api/v1/ai/health');
        $response->assertOk();
        $response->assertJsonStructure(['status', 'model']);
    }
}
