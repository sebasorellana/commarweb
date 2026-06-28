<?php
$articlePath = $articlePath ?? basename($_SERVER['PHP_SELF'] ?? '');
$articleTitle = $articleTitle ?? 'Artículo de COMMAR GROUP';
$articleUrl = commar_absolute_url(commar_url($articlePath));
$encodedArticleUrl = rawurlencode($articleUrl);
$encodedArticleTitle = rawurlencode($articleTitle);
$encodedWhatsappText = rawurlencode($articleTitle . ' - ' . $articleUrl);
?>
<footer class="article-footer">
    <a href="<?php echo htmlspecialchars(commar_url('blog.php'), ENT_QUOTES, 'UTF-8'); ?>" class="article-back-link"><?php echo htmlspecialchars(commar_t('article.back'), ENT_QUOTES, 'UTF-8'); ?></a>
    <div class="article-signature">COMMAR GROUP</div>
    <div class="article-share" aria-label="<?php echo htmlspecialchars(commar_t('article.share'), ENT_QUOTES, 'UTF-8'); ?>">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo htmlspecialchars($encodedArticleUrl, ENT_QUOTES, 'UTF-8'); ?>" class="article-share-link" target="_blank" rel="noopener noreferrer" aria-label="Compartir en Facebook">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h2V5h-2c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.4l.6-3h-3V9c0-.6.4-1 1-1Z"/></svg>
        </a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo htmlspecialchars($encodedArticleUrl, ENT_QUOTES, 'UTF-8'); ?>&title=<?php echo htmlspecialchars($encodedArticleTitle, ENT_QUOTES, 'UTF-8'); ?>" class="article-share-link" target="_blank" rel="noopener noreferrer" aria-label="Compartir en LinkedIn">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 9H3.4v12h3.1V9Zm.2-3.7A1.8 1.8 0 1 0 3 5.2a1.8 1.8 0 0 0 3.7.1ZM21 14.4c0-3.2-1.7-5.2-4.3-5.2-1.5 0-2.5.8-3 1.6V9H10.7v12h3.1v-6.4c0-1.7.8-2.7 2.1-2.7s2 1 2 2.8V21H21v-6.6Z"/></svg>
        </a>
        <a href="https://twitter.com/intent/tweet?url=<?php echo htmlspecialchars($encodedArticleUrl, ENT_QUOTES, 'UTF-8'); ?>&text=<?php echo htmlspecialchars($encodedArticleTitle, ENT_QUOTES, 'UTF-8'); ?>" class="article-share-link" target="_blank" rel="noopener noreferrer" aria-label="Compartir en X">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.8 3h3.4l-7.5 8.6 8.8 9.4h-6.9l-5.4-5.8L3 21h-3.4l8-9.1L-.8 3h7.1l4.9 5.3L16.8 3Zm-1.2 16.3h1.9L5.3 4.6H3.2l12.4 14.7Z"/></svg>
        </a>
        <a href="https://wa.me/?text=<?php echo htmlspecialchars($encodedWhatsappText, ENT_QUOTES, 'UTF-8'); ?>" class="article-share-link" target="_blank" rel="noopener noreferrer" aria-label="Compartir por WhatsApp">
            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M19.1 17.2c-.3-.1-1.6-.8-1.8-.9-.2-.1-.4-.1-.6.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-1.6-.8-2.7-1.4-3.8-3.1-.3-.5.3-.5.8-1.5.1-.2.1-.3 0-.5s-.6-1.5-.8-2c-.2-.5-.4-.4-.6-.5h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.2 0 1.3.9 2.5 1.1 2.7.1.2 1.9 2.8 4.5 4 .6.3 1.1.4 1.5.6.6.2 1.2.2 1.7.1.5-.1 1.6-.6 1.8-1.3.2-.6.2-1.2.2-1.3-.1-.1-.3-.2-.5-.3Z"/><path d="M16 4.5a11.4 11.4 0 0 0-9.8 17.2L4.8 27l5.5-1.4A11.4 11.4 0 1 0 16 4.5Zm0 20.9c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.3.9.9-3.2-.2-.3A9.5 9.5 0 1 1 16 25.4Z"/></svg>
        </a>
    </div>
</footer>
