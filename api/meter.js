/*EVENTS - on click, etc*/
$(document).ready(function() {
    //Submit meter readings
    $("body").on("click","#insert-submit",
    function(event) {
        event.preventDefault();
        //console.log("Submitting form.");
        
        var resolution = "Hour"; //Magic interval
        var day = $("#insert-form [name='day']")[0]["value"];
        
        var meter = {
            meter_id: $("#insert-form [name='meter_id']")[0]["value"],
            customer_id: $("#insert-form [name='customer_id']")[0]["value"],
            resolution: resolution,
            from: day+"T00:00:00Z",
            to: day+"T23:00:00Z",
            values: {}
        }
        
        $(".insert-value").each(function() {
            var name = day + $(this)[0]["name"];
            var value = $(this)[0]["value"];
            meter.values[name] = value;
        });
        //console.log(meter);
        
        var output = JSON.stringify(meter);
        //console.log(output);
        
        $.ajax({
            url: $("#insert-form").attr("action"),
            method: $("#insert-form").attr("method"),
            dataType: 'json',
            data: output,
            success: function(data, status, description) {
                //console.log("Transaction completed.");
                //console.log(data, status, description);
            },
            error: function(data, status, description) {
                //console.log("Transaction failed.");
                //$("#log").append(data.responseText);
                //console.log(data, status, description);
            }
        });
    });
    
    //Load customer's meters.
    $("body").on("click","#customer-form",
    function(event) {
        event.preventDefault();
        //console.log("Fetching meters.");
        
        var input = $("#customer-form").serialize();
        get_meters(input);
    });
    
    //Load specified meter.
    $("body").on("click","input.loadmeter",
    function(event) {
        event.preventDefault();  //Button shouldn't have one that I care about, but just to be safe.
        var meter = $(this).attr("name");
        //console.log("Loading specified meter: "+meter);
        
        var from = $("#"+meter+" [name='meterfrom']").val()+"T00:00:00Z";
        var to   = $("#"+meter+" [name='meterto']"  ).val()+"T23:00:00Z";
        //console.log(meter, from, to);
        
        get_meter(meter, from, to);
    });
    
    //Load specified meter's total power usage for period
    $("body").on("click","input.loadmetertotal",
    function(event) {
        event.preventDefault(); //Shouldn't have a noteworthy default, but we disable it anyway.
        var meter = $(this).attr("name");
        console.log("Loading specified meter's total: "+meter);
        
        var from = $("#"+meter+" [name='meterfrom']").val()+"T00:00:00Z";
        var to   = $("#"+meter+" [name='meterto']"  ).val()+"T23:00:00Z";
        //console.log(meter, from, to);
        
        get_total(meter, from, to)
    });
});

/*GET*/
//Fetches all meters associated with customer
function get_meters(input) {
    $.ajax({
        url: 'api/meter.php?meters',
        method: 'get',
        dataType: 'json',
        data: input,
        success: function(data, status, description) {
            //console.log("Succesfully fetched meters.");
            //console.log(data,status,description);
            print_meters(data);
        },
        error: function(data, status, description) {
            //console.log("Failed to fetch meters.");
            //$("#log").append(data.responseText);    //Prints if something interesting happens.
            //console.log(data,status,description);
        }
    })
}

//Fetches all readings from meter in specified timespan
function get_meter(meter, from, to) {
    $.ajax({
        url: "api/meter.php?get",
        method: "get",
        dataType: 'json',
        data: {"meter_id":meter,"from": from, "to": to},
        success: function(data, status, description) {
            //console.log("Successfully loaded meter.");
            //console.log(data,status,description);
            print_meter(data);
        },
        error: function(data, status, description) {
            //console.log("Failed to load meter.");
            //console.log(data,status,description);
        }
    })
}

//Fetches total kw from meter in specified timespan
function get_total(meter, from, to) {

    $.ajax({
        url: "api/meter.php?total",
        method: "get",
        dataType: 'json',
        data: {"meter_id":meter,"from": from, "to": to},
        success: function(data, status, description) {
            //console.log("Successfully loaded meter.");
            //console.log(data,status,description);
            print_total(data, meter, from, to);
        },
        error: function(data, status, description) {
            //console.log("Failed to load meter.");
            //console.log(data,status,description);
            //$("#log").append(data.responseText);
        }
    })
}

/*PRINT*/
//Prints out the names of every meter passed to it.
function print_meters(meters) {
    //console.log(meters);
    $("#meters").empty();
    
    var today = $("#hidden_clock").val();
    var weekago = $("#hidden_past").val();
    
    var wa = weekago+"T00:00:00Z";  //Weekago, fixed
    var td = today+"T23:00:00Z";    //Today, fixed
    //console.log(today);
    
    $.each(meters, function(index, value) {
       $("#meters").append("<div id="+value+" ><h1>Måler "+value+"</h1><p class='powertotal'></p><input type='date' name='meterfrom'  value="+today+" /><input type='date' name='meterto' value="+today+" /><input type='button' class='loadmeter' value='Last målte verdier' name="+value+" /><input type='button' class='loadmetertotal' value='Last totalbruk for perioden' name="+value+" /><div class='metercontainer'></div></div>");
       
       get_total(value,wa,td);
    });
}

//Prints out the details of a specified meter passed to it.
function print_meter(meter) {
    //console.log(meter);
    var meter_id = meter['meter_id'];
    var print_area = "#"+meter_id+" .metercontainer";
    $(print_area).empty();
    
    $.each(meter["values"],function(time, value) {
        $(print_area).append("<div><span>"+time+"</span> <span>"+value+"</span></div>");
    })
}

//Prints total power measured.
function print_total(kw, meter, from, to) {
    var print_area = "#"+meter+">p.powertotal";
    $(print_area).empty();
    
    $(print_area).append("<b>"+kw["kw"]+" kw</b> totalbruk i perioden "+from+" til "+to);
}