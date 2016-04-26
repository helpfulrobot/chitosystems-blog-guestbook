<div class="container-fluid">

    <article id="Layout" class="full-float">

        <div class="row">
            <aside class="col-sm-4 col-md-3">
                <% include PageSidebar %>
            </aside>
            <div class="col-sm-8 col-md-9">
                <section id="ContentArea">

                    <h1 class="main-title orange font-intro-rust">{$CustomPageTitle}</h1>

                    <% if $SubTitle %>
                        <h2 class="sub-title">{$SubTitle}</h2>
                    <% end_if %>

                    {$Content}
                    {$BlogGuestBookForm}

                </section>


            </div>
        </div>


    </article>

</div>