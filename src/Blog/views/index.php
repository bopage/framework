<?= $renderer->render('header') ?>
<h1>Bienvenue le Monde</h1>
<ul>
    <li><a href=<?=$router->generateUri('blog.show', ['slug' => 'zerrer-7afeff'])?>>Article 1</a> </li>
    <li>Article 1</li>
</ul>
<?= $renderer->render('footer') ?>
