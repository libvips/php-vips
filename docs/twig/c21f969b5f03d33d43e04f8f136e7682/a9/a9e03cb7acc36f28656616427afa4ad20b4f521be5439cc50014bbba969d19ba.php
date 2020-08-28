<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* base.html.twig */
class __TwigTemplate_3d6432f8407889ba9f37dfc8e61c30233847c5776449830c5069b11880ab9a31 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'javascripts' => [$this, 'block_javascripts'],
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"utf-8\">
    <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
    <meta name=\"description\" content=\"\">
    <meta name=\"author\" content=\"\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro\">
    <link rel=\"stylesheet\" href=\"";
        // line 10
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["css/normalize.css"]), "html", null, true);
        echo "\">
    <link rel=\"stylesheet\" href=\"";
        // line 11
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["css/template.css"]), "html", null, true);
        echo "\">
    <link rel=\"icon\" href=\"";
        // line 12
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["images/favicon.ico"]), "html", null, true);
        echo "\"/>
    ";
        // line 13
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 14
        echo "    <script src=\"https://cdn.jsdelivr.net/gh/google/code-prettify@master/loader/run_prettify.js\"></script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/fuse.js/3.4.6/fuse.min.js\"></script>
    <script src=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["js/search.js"]), "html", null, true);
        echo "\"></script>
    <script defer src=\"";
        // line 17
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), ["js/searchIndex.js"]), "html", null, true);
        echo "\"></script>
    ";
        // line 18
        $this->displayBlock('javascripts', $context, $blocks);
        // line 19
        echo "</head>
<body>
<header class=\"phpdocumentor-top-header\"></header>
<header class=\"phpdocumentor-header\">
    <section class=\"phpdocumentor-section\">
        <h1 class=\"phpdocumentor-title\">";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "name", [], "any", false, false, false, 24), "html", null, true);
        echo "</h1>
    </section>
</header>
<main class=\"phpdocumentor\">
    <div class=\"phpdocumentor-section\">
        ";
        // line 29
        $this->loadTemplate("sidebar.html.twig", "base.html.twig", 29)->display($context);
        // line 30
        echo "
        <div class=\"nine phpdocumentor-columns phpdocumentor-content\">
        ";
        // line 32
        $this->displayBlock('content', $context, $blocks);
        // line 33
        echo "            <div data-search-results class=\"phpdocumentor-search-results phpdocumentor-search-results--hidden\">

                <h2>Search results</h2>
                <ul class=\"phpdocumentor-search-results__entries\">

                </ul>
            </div>
        </div>
    </div>
</main>

</body>
</html>
";
    }

    // line 5
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["project"] ?? null), "name", [], "any", false, false, false, 5), "html", null, true);
    }

    // line 13
    public function block_stylesheets($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 18
    public function block_javascripts($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    // line 32
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  142 => 32,  136 => 18,  130 => 13,  123 => 5,  106 => 33,  104 => 32,  100 => 30,  98 => 29,  90 => 24,  83 => 19,  81 => 18,  77 => 17,  73 => 16,  69 => 14,  67 => 13,  63 => 12,  59 => 11,  55 => 10,  47 => 5,  41 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "base.html.twig", "base.html.twig");
    }
}
