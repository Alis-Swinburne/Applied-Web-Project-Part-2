<?php 
$page_title = "About";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>

<main>
    <h2 style="color:darkblue"> About Our Team</h2>
        
    <h3>Group Information</h3>
    <ul>
        <li>Group Name: Courageous Coders
            <ul>
                <li>Tutorial Class: Thursday 12:30-2:30 PM</li>
            </ul>
        </li>
    </ul>
        
    <h3>Member Contributions</h3>
    <dl>
        <dt><span class="student-number">106505815</span> Juliette Grove</dt>
        <dd> Shared responsibility: CSS File, About Page and individual responsibility: About page  .</dd>
        
        <dt><span class="student-number">106154958</span> Ada Yong</dt>
        <dd>Shared responsibility: CSS File, About Page and individual responsibility: Apply page .</dd>
        
        <dt><span class="student-number">106507921</span> Andrea McPadden</dt>
        <dd>Shared responsibility: CSS File, About Page and individual responsibility: Jobs page .</dd>
        
        <dt><span class="student-number">106843502</span> Alisla Foley</dt>
        <dd> Shared responsibility: CSS File, About Page and individual responsibility: Index/Home Page, Header (Navigation bar), and Footer  .</dd>
    </dl>
        
    <h3>Team Quotes</h3>
        
    <p><strong>Juliette Grove:</strong> "Tutto scorre." – Italian  
    <br>English: "Everything flows."</p>
        
    <p><strong>Ada Yong:</strong> "船到桥头自然直" - Chinese
    <br>English: "The boat will go straight when it reaches the bridge."</p>
        
    <p><strong>Andrea McPadden:</strong> "Цена — это то, что вы платите. Ценность — это то, что вы получаете." - Russian
    <br>English: "Price is what you pay. Value is what you get."</p>
        
    <p><strong>Alisla Foley:</strong> "L'argent coûte souvent trop cher." - French
    <br>English: "Money often costs too much."</p>
        
    <h3> Group Photo</h3>
    <figure>
    <img src="images/group_photo.jpeg" alt="Group Photo of Courageous Coders" width="600" height="400">
    <figcaption>The Courageous Coders in our Thursday tutorial.</figcaption>
    </figure>
        
    <h3>Fun Facts About Our Team</h3>
        
    <table id="about-table">    
        <caption>Fun Facts About Team Members</caption>
        <tr>
            <th>Name</th>
            <th>Fact</th>
        </tr>
        <tr>
            <td>Juliette Grove</td>
            <td>Has been to 10 countries</td>
        </tr>
        <tr>
            <td>Ada Yong</td>
            <td>Has a favourite spoon</td>
        </tr>
        <tr>
            <td>Andrea McPadden</td>
            <td>Has read 60 books this year.</td>
        </tr>
        <tr>
            <td>Alisla Foley</td>
            <td>Her favourite colour is red.</td>
        </tr>
    </table>
</main>

<?php require_once 'includes/footer.inc'; ?>
