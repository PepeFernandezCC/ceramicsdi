
    <div class="faq-data">      
        <article id="faq">
            {foreach from=$faq item=$question}
                <button class="accordion">
                    {$question.title}
                </button>
                <section style="display: none;">
                    {$question.answer nofilter}
                </section>
            {/foreach}
        </article>
    <div>
