/* Private variables */
var totalNumberOfCalls = 0;
var timePeriodInSeconds = 0;
var averageCallDuration = 0;
var numberOfAgents = 0;
var targetAnswerTime = 0;

var averageArrivalRate = 0;
var trafficIntensity = 0;
var agentOccupancy = 0;
var probabilityOfWaiting = 0;
var averageSpeedOfAnswer = 0;
var serviceLevel = 0;

jQuery(function ($){
	var Common = {
		init: function (){
			jQuery("body").on('click','.type_calculator',this.handletType);
            jQuery("body").on('click','.submit_button',this.handleSubmit);
		},
        handletType : function(){
            $('.Manual_div').show();
            if($(this).val() == '1'){
                $('.Manual_div').show();
                $('.Dynamic_div').hide();
                $('.submit_button').show();
                $('.submit_cal_button').addClass('submit_button');
            }else{
                $('#datetimepicker1').datetimepicker();
                $('.Dynamic_div').show();
                $('.Manual_div').hide();
                $('.submit_button').show();
                $('.submit_cal_button').removeClass('submit_button');
            }
           
        },
        handleSubmit : function(){
            totalNumberOfCalls = Common.getValueAsFloat( 'totalNumberOfCalls' );
            timePeriodInSeconds = Common.getValueAsFloat( 'timePeriodInSeconds' );
            averageCallDuration = Common.getValueAsFloat( 'averageCallDuration' );
            numberOfAgents = Common.getValueAsFloat( 'numberOfAgents' );
            targetAnswerTime = Common.getValueAsFloat( 'targetAnswerTime' );
            Common.calculate();
            // Common.write();
            $('.erlang_output').show();
            $('.table_list').show();
        },
        /**
          * Reads a textbox and converts its value into an int.
          */
        getValueAsFloat: function ( id ) {
            console.log('yes');
          var element = document.getElementById( id );
          var value = element !== null ? element.value.trim().replace( / /g, '' ) : null;
          var int = parseFloat( value );
          return !isNaN( int ) ? int : 0;
        },

        /* Private functions */
        /**
        * Calculates the Erlang-C formula using all available data.
        */
        calculate : function () {
            // Average arrival rate in seconds = total number of calls / time period in seconds
            averageArrivalRate = timePeriodInSeconds > 0 ? totalNumberOfCalls / timePeriodInSeconds : 0;

            // Traffic intensity = average arrival rate * average call duration
            trafficIntensity = averageArrivalRate * averageCallDuration;

            // Agent occupancy, or utilitization.
            agentOccupancy = numberOfAgents > 0 ? trafficIntensity / numberOfAgents : 0;

            // Ec(m,u) = probability that a call is not answered immediately and has to
            // wait. This will be a decimal between 0 and 1.
            // Numerator in this formula is:
            // (traffic intensity ^ number of agents / number of agents factorial)
            // Sigma notation means for every number k from 0 to (number of agents - 1),
            // add (traffic intensity ^ k) / (k factorial)
            var powNumerator = numberOfAgents > 0 ?
            Math.pow( trafficIntensity, numberOfAgents ) /
            Common.factorial( numberOfAgents ) :
            0;
            var powDenominator = powNumerator +
            ( ( 1 - agentOccupancy ) * Common.erlangSigma() );
            probabilityOfWaiting = powNumerator / powDenominator;

            // Average speed of answer (wait time, response time)
            // Tw = (probability of waiting * average call duration) /
            // (number of agents * (1 - agent occupancy))
            averageSpeedOfAnswer = numberOfAgents > 0 && agentOccupancy !== 1 ?
            ( probabilityOfWaiting * averageCallDuration ) /
            ( numberOfAgents * ( 1 - agentOccupancy ) ) :
            0;

            var eExponent = averageCallDuration > 0 ?
            ( numberOfAgents - trafficIntensity ) * -1 *
            ( targetAnswerTime / averageCallDuration ) :
            0;
            serviceLevel = 1 - ( probabilityOfWaiting * Math.exp( eExponent ) );

            // console.log( 'probabilityOfWaiting: ' + probabilityOfWaiting );
            // console.log( 'averageCallDuration: ' + averageCallDuration );
            // console.log( 'numberOfAgents: ' + numberOfAgents );
            // console.log( 'agentOccupancy: ' + agentOccupancy );
            // console.log( 'p: ' + ( 1 - agentOccupancy ) );
            // console.log( 'trafficIntensity: ' + trafficIntensity );
            // console.log( 'averageSpeedOfAnswer: ' + averageSpeedOfAnswer );
            // console.log( 'targetAnswerTime: ' + targetAnswerTime );
            // console.log( 'e exponent: ' + eExponent );
 
          /**
          * Writes the calculated values to the appropriate textboxes.
          */
            var timeUnit = 'second';
            document.getElementById( 'averageArrivalRate' ).value = averageArrivalRate + ' calls / ' + timeUnit;

            document.getElementById( 'trafficIntensity' ).value = trafficIntensity;

            document.getElementById( 'agentOccupancy' ).value = ( agentOccupancy * 100 ).toFixed( 2 ) + '%';

            document.getElementById( 'probabilityOfWaiting' ).value = ( probabilityOfWaiting * 100 ).toFixed( 2 ) + '%';

            document.getElementById( 'averageSpeedOfAnswer' ).value = averageSpeedOfAnswer.toFixed( 2 ) + ' ' + timeUnit + 's';

            document.getElementById( 'serviceLevel' ).value = ( serviceLevel * 100 ).toFixed( 2 ) + '%';

            // changes by aarti
            var contacts = Common.getValueAsFloat( 'timePeriodInSeconds' );
            var probabilityOfWaiting_1 = ( probabilityOfWaiting * 100 ).toFixed( 2 ) + '%';
            var agentOccupancy_1 = ( agentOccupancy * 100 ).toFixed( 2 ) + '%';
            var averageSpeedOfAnswer_1 = averageSpeedOfAnswer.toFixed( 2 ) + ' ' + timeUnit + 's';

            $('#agents').find('.erlang-results-value').text(averageArrivalRate);
            $('#serviceLevel').find('.erlang-results-value').text(probabilityOfWaiting_1);
            $('#occupancy').find('.erlang-results-value').text(agentOccupancy_1);
            $('#contacts').find('.erlang-results-value').text(contacts);

            $('.average_rate').text(averageArrivalRate);
            $('.occupancy').text(agentOccupancy_1);
            var serviceLevel_val = ( serviceLevel * 100 ).toFixed( 2 ) + '%';
            $('.Service_level').text(serviceLevel_val);
            $('.Answer_time').text(averageSpeedOfAnswer_1);
            $('.waiting').text(probabilityOfWaiting_1);
        },
        factorial : function( number ) {
            if ( number === 0 ) { return 1; }
            return number * Common.factorial( number - 1 );

        },
        erlangSigma:function(){
          var output = 0;
          for ( var k = 0; k < numberOfAgents; k++ ) {
              output += ( Math.pow( trafficIntensity, k ) / Common.factorial( k ) );
          }
          return output;
        }
	}
	Common.init();
});
$(function () {
 $('#datetimepicker1').datetimepicker();
});
