<?php 
$page_title = "Jobs";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>

<main>
    <section class="intro-section">
        <h2 style="color: black; text-align:left">Current Career Opportunities</h2> 
        <p>Join the FinTech team &amp; shape the future of digital banking and personal finance technology</p>
        
        <!-- Perks section moved here: inside the intro, above the directory -->
        <aside class="perks-sidebar">
            <h3>Why work at Fintech?</h3>
            <p>We believe our employees do their best work when they feel supported, challenged and rewarded.</p>
            <ul>
                <li><strong>Flexible Work:</strong> Hybrid and fully remote work options.</li>
                <li><strong>Learning &amp; Development:</strong> $2000 annual budget per employee for courses and certifications.</li>
                <li><strong>Health &amp; Wellness:</strong> Comprehensive health insurance and complimentary mental health days.</li>
            </ul>
        </aside>

        <!-- Quick Navigation Directory -->
        <section class="job-directory">
            <h3>Open Positions Overview</h3>
            <p>Click on a job title below to jump directly to the position details:</p>
            <ul>
                <li><a href="#DEV042">Senior Full Stack Developer (Ref: DEV042)</a></li>
                <li><a href="#SEC918">Cybersecurity Analyst (Ref: SEC918)</a></li>
                <li><a href="#DAT551">Financial Data Analyst (Ref: DAT551)</a></li>
            </ul>
        </section>
    </section>

    <article class="job-card" id="DEV042">
        <header>
            <h3>Senior Full Stack Developer</h3>
            <p><strong>Reference Number:</strong> DEV042</p>
            <p><strong>Salary:</strong> $135,000 &ndash; $155,000 per annum + Super</p>
            <p><strong>Reporting Line:</strong> Engineering Lead</p>
        </header>

        <p>We are seeking an experienced Full Stack Developer to lead the engineering of our core web applications, ensuring high scalability, performance, and security across all platforms</p>

        <section>
            <h4>Key Responsibilities</h4>
            <ul>
                <li>Architect, build, and maintain robust client-side and server-side applications.</li>
                <li>Collaborate with UI/UX designers to implement modern, responsive interfaces.</li>
                <li>Integrate secure third-party financial and payment gateway APIs.</li>
                <li>Conduct code reviews, continuous integration, and mentor software developers.</li>
            </ul>
        </section>

        <section>
            <h4>Requirements</h4>
            <h5>Essential Requirements</h5>
            <ol>
                <li>5<sup>+</sup> years of experience with HTML5, CSS3, JavaScript, and REST APIs.</li>
                <li>Demonstrated expertise in web application security standards.</li>
                <li>Strong working knowledge of relational and NoSQL databases.</li>
            </ol>

            <h5>Preferable Requirements</h5>
            <ul>
                <li>Experience building applications within the FinTech or Banking sector.</li>
                <li>Familiarity with cloud infrastructures and CI/CD pipelines.</li>
            </ul>
        </section>

        <div class="apply-link-container">
            <a href="apply.php?ref=DEV042" target="_self" class="apply-btn">Apply for this Position</a>
        </div>
    </article>
    
    <article class="job-card" id="SEC918">
        <header>
            <h3>Cybersecurity Analyst</h3>
            <p><strong>Reference Number:</strong> SEC918</p>
            <p><strong>Salary:</strong> $115,000 &ndash; $130,000 per annum + Super</p>
            <p><strong>Reporting Line:</strong> Chief Information Security Officer (CISO)</p>
        </header>

        <p>Protect our financial platform by continuously monitoring threats, assessing vulnerabilities, and enforcing security compliance across all operational systems.</p>

        <section>
            <h4>Key Responsibilities</h4>
            <ul>
                <li>Monitoring networks and systems for security breaches, intrusions, and anomalous activity.</li>
                <li>Conduct routine vulnerability assessments and coordinate penetration testing.</li>
                <li>Develop, maintain, and execute comprehensive incident response procedures.</li>
                <li>Ensure platform adherence to security frameworks like PCI-DSS.</li>
            </ul>
        </section>

        <section>
            <h4>Requirements</h4>
            <h5>Essential Requirements</h5>
            <ol>
                <li>3<sup>+</sup> years in a specialized Information Security or Security Operations role.</li>
                <li>Strong understanding of cryptographic protocols, network protocols, and firewalls.</li>
                <li>Relevant security certification (e.g., CISSP, CISM, or CompTIA Security+).</li>
            </ol>

            <h5>Preferable Requirements</h5>
            <ul>
                <li>Degree in Cybersecurity, Computer Science, or Information Technology.</li>
                <li>Hands-on experience with SIEM tools and threat monitoring tools</li>
            </ul>
        </section>

        <div class="apply-link-container">
            <a href="apply.php?ref=SEC918" target="_self" class="apply-btn">Apply for this Position</a>
        </div>
    </article>

    <article class="job-card" id="DAT551">
        <header>
            <h3>Financial Data Analyst</h3>
            <p><strong>Reference Number:</strong> DAT551</p>
            <p><strong>Salary:</strong> $95,000 &ndash; $110,000 per annum + Super</p>
            <p><strong>Reporting Line:</strong> Analytics Manager</p>
        </header>

        <p>Translate customer transaction data and financial trends into actionable business insights and drive product strategies, fraud prevention models, and user engagement.</p>

        <section>
            <h4>Key Responsibilities</h4>
            <ul>
                <li>Extract, transform, and analyze complex datasets to identify spending behavior patterns.</li>
                <li>Design interactive data dashboards and reporting systems for executive stakeholders.</li>
                <li>Partner with product teams to validate hypotheses using A/B testing methodologies.</li>
                <li>Assist risk management teams with fraud detection trend analysis.</li>
            </ul>
        </section>

        <section>
            <h4>Requirements</h4>
            <h5>Essential Requirements</h5>
            <ol>
                <li>2<sup>+</sup> years of experience in Data Analytics or Business Intelligence.</li>
                <li>Advanced proficiency with SQL and statistical tools.</li>
                <li>Solid foundation in statistical analysis and predictive modeling techniques.</li>
            </ol>

            <h5>Preferable Requirements</h5>
            <ul>
                <li>Background in quantitative finance of consumer lending metrics.</li>
                <li>Familiarity with automated data pipeline technologies.</li>
            </ul>
        </section>

        <div class="apply-link-container">
            <a href="apply.php?ref=DAT551" target="_self" class="apply-btn">Apply for this Position</a>
        </div>
    </article>
</main>

<?php require_once 'includes/footer.inc' ?>