// https://stackoverflow.com/questions/23593052/format-javascript-date-as-yyyy-mm-dd

var date = new Date();
var d_temp = new Date();
d_temp.setMonth(date.getMonth() + 2);

var d1 = date.toISOString().split("T")[0];
var d2 = d_temp.toISOString().split("T")[0];

var t1 = date.toLocaleTimeString();
console.log(t1);

$("#command_commandAt_date").attr("min", d1);
$("#command_commandAt_date").attr("max", d2);
$("#command_commandAt_date").attr("max", d2);
$("#command_commandAt_date").attr("max", d2);
