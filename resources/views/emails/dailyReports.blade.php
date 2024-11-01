<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; box-sizing: border-box; background-color: #ffffff; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }

        }
    </style>

    <h1 style="text-align: center; padding-top: 2rem;"> Daily Report ({{$todaysDate}}) </h1>
    <div style="padding: 20px;background-color: #e6ffff;overflow-x:auto;">
        <table style="width:100%; border-collapse: collapse; font-size: 0.9em;font-family: sans-serif;min-width: 400px;box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Bogus Net Platform Gain (with bonuses)</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['bogusNetProfit']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">True Net Platform Gain (without bonuses)</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['trueNetProfit']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; ">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Fundings</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalFundedAmount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Withdrawals</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalWithdrawals']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; ">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Staked Amount</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalStakedAmount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Amount Won</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalAmountWon']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Completed Stake Sessions Count</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['completedStakingSessionsCount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Incomplete Stake Sessions Count</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['incompleteStakingSessionsCount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Used Boosts Count</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalUsedBoostCount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Unique Stakers Count</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['uniqueStakersCount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Purchased Boosts Count</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalPurchasedBoostCount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Bonus Stakes Amount</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalBonusStakesAmount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Bonus Winnings Amount</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{$data['totalBonusWinningsAmount']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Total Challenge Sessions</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalChallengeSessions']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Number Of Users That Played Challenge</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalChallengePlayers']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Number Of Users That Won Challenge</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalChallengeWinners']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Number Of Users That Lost Challenge</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalChallengeLosers']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Number Of Challenge Draws</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['totalChallengeDraws']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Amount Won By Challenge Bot</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['AmountWonByChallengeBot']}}</td>
            </tr>
            <tr style=" color: gray;text-align: left; background-color: #dddddd;">
                <th style="padding-top: 12px; padding-bottom: 12px; border: 1px solid #dddddd;text-align: left;padding: 8px;">Amount Won By Challenge Players</th>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data['AmountWonByChallengePlayers']}}</td>
            </tr>
        </table>
    </div>
    <h3 style="text-align: center;padding-top: 2rem;"> Top {{count($data['stakers'])}} Stakers </h3>
    <div style="padding: 20px;background-color: #e6ffff;overflow-x:auto;">
        <table style="width:100%; border-collapse: collapse; font-size: 0.9em;font-family: sans-serif;min-width: 400px;box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">
            <tr style=" color: gray;text-align: left;">
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">#</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Username</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Email</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Amount Staked</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Amount Won</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Profit</th>
                <th style="padding-top: 12px; padding-bottom: 12px;text-align: left;  border: 1px solid #dddddd;text-align: left;padding: 8px;">Profit Percentage</th>
            </tr>
            @foreach($data['stakers'] as $key=>$data)
            <tr style=" color: gray;text-align: left;">
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$key + 1}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data->username}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{$data->email}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{number_format($data->staked)}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">₦{{number_format($data->won)}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{number_format($data->profit)}}</td>
                <td style="border: 1px solid #dddddd;text-align: left;padding: 8px;">{{number_format($data->profit_perc)}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>

</html>