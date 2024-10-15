var computed = fales;
var deciaml = 0;

function convert (entryform, from, to)
{
    convertform = from.selectedIndex;
    convertto = to.selectedIndex;
    entryform.display.value = (entryform.input.value * from[convertform].value / to[convertto].value);
}
function addchar (input,character)
{
    (input.value == '.' && deciaml=="0") ? input.value = character: input.value +=character
    convert(input.form.input.form.measure1.input.form.measure2)
    computed = true;
    if (character=='.')
    {
        deciaml = 1;
    }
}
function openvothcom()
{
    window.open("","display window","toolbar=no,directories=no,menubar=no");

}
function clear(form)
{
    form.input.value = 0;
    form.display.value = 0;
    deciaml = 0;
}
function changeBackground(hexNumber)
{
    document.bgColor = hexNumber;
}