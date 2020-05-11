<h2>Production</h2>

<p>
    Production <?php echo $data['production']['last_vs_prev']['past'] ?> for the firm averaging <?php echo $data['production']['last_av'] ?> per day last week
    compared to the previous weeks <?php echo $data['production']['prev_av'] ?> a <?php echo $data['production']['last_vs_prev']['gain_loss'] ?> of <?php echo $data['production']['last_vs_prev']['percentage_change'] ?>%
    <?php $data['production']['prev_vs_av_join'] ?> shows a <?php echo $data['production']['last_vs_av']['trend'] ?>wards trend in comparison to the previous <?php echo $weeks ?> weeks.
    <br>
    This level of production is <?php echo $data['production']['range']['text'] ?> accepted daily range of <?php echo $data['production']['range']['low'] ?> - <?php echo $data['production']['range']['high'] ?> / day.
</p>

<?php

if($data['production']['BusinessGroups']) { ?>

    <p>Among the individual business groups: </p>
    <ul>
        <?php foreach ($data['production']['BusinessGroups'] as $group_name => $group) { ?>
            <li style="margin: 10px 0;">
                <?php echo $group['last_vs_av']['icon'] ?>
                <?php echo $group_name ?> showed <?php echo $group['last_vs_av']['present'] ?> averaging <?php echo $group['last_av'] ?> per day last week
                compared to the previous weeks <?php echo $group['prev_av'] ?> a <?php echo $group['last_vs_prev']['gain_loss'] ?> of <?php echo $group['last_vs_av']['percentage_change'] ?>%
                <?php $data['production']['prev_vs_av_join'] ?> shows a <?php echo $group['last_vs_av']['trend'] ?>wards trend in comparison to the previous <?php echo $weeks ?> weeks.
                <br>
                This is <?php echo $group['range']['text'] ?> accepted daily range of <?php echo $group['range']['low'] ?> - <?php echo $group['range']['high'] ?> / day.
            </li>
        <?php } ?>
    </ul>


<?php } ?>

<h2>Cases Opened</h2>

<p>
    The number of cases opened shows <?php echo $data['cases_opened']['last_vs_av']['present'] ?>  over the past <?php echo $weeks ?> weeks averaging
    <?php echo $data['cases_opened']['last_av'] ?> cases per day compared to a more typical <?php echo $data['cases_opened']['av'] ?> cases per day.
    <?php echo ucfirst($data['cases_opened']['last_vs_prev']['trend']) ?> <?php echo $data['cases_opened']['last_vs_prev']['percentage_change'] ?>% on the previous week.
</p>

<?php

if($data['cases_opened']['BusinessGroups']) { ?>

    <p>Among the individual business groups: </p>

    <ul>
        <?php foreach ($data['cases_opened']['BusinessGroups'] as $group_name => $group) { ?>
            <li style="margin: 10px 0;">
                <?php echo $group['last_vs_av']['icon'] ?>
                <?php echo $group_name ?> cases opened <?php echo $group['last_vs_av']['past'] ?>
                with a <?php echo $group['last_vs_av']['gain_loss'] ?> of <?php echo $group['last_vs_av']['percentage_change'] ?>%
                over the past <?php echo $weeks ?> weeks averaging <?php echo $group['last_av'] ?> cases compared to a more typical <?php echo $group['av'] ?> cases per day.

            </li>
        <?php } ?>
    </ul>


<?php } ?>

<h2>Cases Filed</h2>

<p>
    The number of cases filed at the IPO's showed <?php echo $data['cases_filed']['last_vs_av']['present'] ?> of  <?php echo $data['cases_filed']['last_vs_prev']['percentage_change']?>%
    averaging <?php echo $data['cases_filed']['last_av'] ?> cases per day compared with <?php echo $data['cases_filed']['prev_av']?> the previous week
    <?php echo $data['cases_filed']['prev_vs_av_join']?> <?php echo $data['cases_filed']['last_vs_av']['past']?> against the <?php echo $weeks?> week average.
</p>


<?php

if($data['cases_opened']['BusinessGroups']) { ?>

    <p>Among the individual business groups: </p>

    <ul>
        <?php foreach ($data['cases_filed']['BusinessGroups'] as $group_name => $group) { ?>
            <li style="margin: 10px 0;">
                <?php echo $group['last_vs_av']['icon'] ?>
                <?php echo $group_name ?> cases opened <?php echo $group['last_vs_av']['past'] ?>
                with a <?php echo $group['last_vs_av']['gain_loss'] ?> of <?php echo $group['last_vs_av']['percentage_change'] ?>%
                over the past <?php echo $weeks ?> weeks averaging <?php echo $group['last_av'] ?> cases compared to a more typical <?php echo $group['av'] ?> cases per day.

            </li>
        <?php } ?>
    </ul>


<?php } ?>

<h2>Invoicing</h2>

<p>
    There is no point in commenting on invoicing on a weekly bases as it has a clearly defined monthly cycle.
    However last month’s invoicing was <?php echo $data['invoicing']['last_vs_prev']['trend'] ?> <?php echo $data['invoicing']['last_vs_prev']['percentage_change'] ?>%
    to <?php echo $data['invoicing']['last_av'] ?> on the previous month <?php echo $data['invoicing']['prev_vs_av_join']?>
    <?php echo $data['invoicing']['last_vs_av']['past'] ?> against the yearly average.
</p>


<?php

if($data['invoicing']['BusinessGroups']) { ?>

    <p>Among the individual business groups: </p>

    <ul>
        <?php foreach ($data['invoicing']['BusinessGroups'] as $group_name => $group) { ?>
            <li style="margin: 10px 0;">
                <?php echo $group['last_vs_av']['icon'] ?>
                <?php echo $group_name ?> invoicing for last month showed <?php echo $group['last_vs_av']['present'] ?> at <?php echo $group['last_av'] ?>.
            </li>
        <?php } ?>
    </ul>


<?php } ?>