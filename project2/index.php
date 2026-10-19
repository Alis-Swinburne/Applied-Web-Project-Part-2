<?php 
$page_title = "Home";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main id="index-main">
    <!-- Fill this section out for each individual page! -->

    <section class="hero-image">
        <header class="hero-text">
            <h1 id="hero-heading">Secure your 
                <span class="hover-icon money">
                    <span class="word">money</span>
                    <i class="fa-solid fa-piggy-bank icon" aria-hidden="true"></i>
                </span>, secure your 
                <span class="hover-icon future">
                    <span class="word">future</span>
                    <i class="fa-regular fa-calendar-days icon" aria-hidden="true"></i>
                </span>.
            </h1>

            <p class="hero-subtext">Interested in working at Finova?</p>
            <a href="jobs.php">JOB LISTINGS</a>
        </header>
    </section>

    <section class="index-section" id="description-section">
        <div class="section-left">
            <h2 style="text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.3)">WHO ARE WE?</h2>
            <p>Finova is a secure financial technology company dedicated to making digital finance safe, simple, and accessible for everyone. 
                We provide services for digital payments, budgeting, account management, and financial education, helping our customers take
                control of their finances. Our platform combines user-friendly technology with strong security measures to ensure a safe and
                easy financial experience. By giving users the right tools and knowledge, Finova aims to make managing money, easier, and more convenient. 
            </p>
        </div>
        <div class="section-right">
            <!--Stock photo found at: https://www.istockphoto.com/photo/close-up-of-three-people-looking-at-financial-data-with-graphs-and-charts-gm2197655235-615602540?utm_source=pixabay&utm_medium=affiliate&utm_campaign=sponsored_photo&utm_content=adp_topbanner_media&utm_term=audit+data+analytics -->
            <img src="images/index/description-image.jpg"
            alt="Employees analysing financial reports"
            loading="lazy">
        </div>
    </section>

    <section class="index-section" id="customer-satisfaction-section">
        <div class="section-left">
            <h3 style="text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.3)">WHY CHOOSE US?</h3>
            <p>The results show that 96.7% of our customers report a great increase in understanding their finances. A further 63.9% of those customers
                found that Finova's tools significantly reduced the amount of time they spent managing their finances. As seen in the adjacent table,
                Finova had a higher average of satisfied customers when it came to using our tools in comparison to other popular financial tech companies.
            </p>
        </div>
        <div class="section-right">
            <div class="table-scroll" role="region" aria-labelledby="ratings-caption" tabindex="0">
                <table>
                    <caption id="ratings-caption">
                        Customer satisfaction ratings out of 10 for each Finova tool, compared with two other companies
                    </caption>
                    <colgroup>
                        <col>
                        <col span=5>
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" rowspan="2">Company</th>
                            <th scope="colgroup" colspan="5"> Rating (0/10)</th>
                        </tr>
                        <tr>
                            <th scope="col" class="tools-table-header">Digital Payments</th>
                            <th scope="col" class="tools-table-header">Budgeting</th>
                            <th scope="col" class="tools-table-header">Account Management</th>
                            <th scope="col" class="tools-table-header">Finance Education</th>
                            <th scope="col">AVERAGE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Finova</th>
                            <td>9.8</td>
                            <td>9.1</td>
                            <td>9.4</td>
                            <td>8.7</td>
                            <td>9.25</td>
                        </tr>
                        <tr>
                            <th scope="row">Company 1</th>
                            <td>7.4</td>
                            <td>8.5</td>
                            <td>7.1</td>
                            <td>6.8</td>
                            <td>7.45</td>
                        </tr>    
                        <tr>
                            <th scope="row">Company 2</th>
                            <td>8.1</td>
                            <td>8.4</td>
                            <td>7.4</td>
                            <td>8.2</td>
                            <td>8.025</td>
                        </tr>  
                    </tbody>            
                </table>
            </div>
        </div>
    </section>

    <section class="index-section" id="inclusivity-section">
        <div class="section-left">
            <h2 style="text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.3)">INCLUSIVITY</h2>
            <p> At Finova, we believe in respecting and including people of all backgrounds, genders, and ethnicities. 
                It is our mission to inspire and develop financial skills in our customers and employees that will help fulfill their 
                monetary goals. We are also committed to promoting equal opportunities and supporting the employment, inclusion, and professional
                development of Aboriginal and Torres Strait Islander peoples.
            </p>
            <p>
                Our Hawthorn office is located on the unceded lands of the Wurundjeri Woi Wurrung people of the Kulin Nation. We acknowledge
                their deep, ongoing connection to this Country and pay our respects to Elders past and present. 
            </p>
        </div>
        <figure class="section-right">
            <!-- Stock photo found at: https://www.istockphoto.com/en/photo/group-of-business-persons-talking-in-the-office-gm2163365819-583720271 -->
            <img src="images/index/inclusivity-image.jpg"
            alt="Employees happy at Finova (join our company!)"
            loading="lazy">
        </figure>
    </section>

</main>

<?php include 'includes/footer.inc'; ?>
