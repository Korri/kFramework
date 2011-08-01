function toggle(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
}

/** Add deaccent method **/
/**
 * @fileoverview
 * @author  Doeke Zanstra, doeke@zanstra.net
 * @licence BSD licence
 *          The copyright for dzLib is owned by Doeke Zanstra. Things are distibuted under
 *          the BSD license, which allows free distribution is most circumstances.
 *          {@link http://www.xs4all.nl/~zanstra/dzLib/legal.htm}
 * This file contains handy extensions on the javascript String type.
 * Methods are added after I wrote them because I needed them. So, don't
 * expect this collection to be complete, or being part of a framework.
 * Enjoy (I mean this, please do so)
 */

/**
 * Mapping table between accented en non-accented lowercase characters,
 * in a format optimized for the String.deaccent method.
 *
 * Mapping ripped from:
 * {@link http://dev.splitbrain.org/reference/dokuwiki/nav.html?inc/utf8.php.source.html}
 * @private
 */
var ACCENT_LOWERCASE={
	a: /[àáâãåāăą]/g,
	ae: /[äæ]/g,
	b: /[ḃ]/g,
	c: /[çćĉċč]/g,
	d: /[ďđḋ]/g,
	dh: /[ð]/g,
	e: /[èéêëēĕėęě]/g,
	f: /[ƒḟ]/g,
	g: /[ĝğġģ]/g,
	h: /[ĥħ]/g,
	i: /[ìíîïĩīį]/g,
	j: /[ĵ]/g,
	k: /[ķ]/g,
	l: /[ĺļľł]/g,
	m: /[ṁ]/g,
	n: /[ñńņň]/g,
	o: /[òóôõøōőơ]/g,
	oe: /[ö]/g,
	p: /[ṗ]/g,
	r: /[ŕŗř]/g,
	s: /[śŝşšșṡ]/g,
	ss: /[ß]/g,
	t: /[ţťŧțṫ]/g,
	th: /[þ]/g,
	u: /[µùúûũūŭůűųư]/g,
	ue: /[ü]/g,
	w: /[ŵẁẃẅ]/g,
	y: /[ýÿŷỳ]/g,
	z: /[źżž]/g
};
/**
 * Mapping table between accented en non-accented uppercase characters,
 * in a format optimized for the String.deaccent method.
 *
 * Mapping ripped from:
 * {@link http://dev.splitbrain.org/reference/dokuwiki/nav.html?inc/utf8.php.source.html}
 * @private
 */
var ACCENT_UPPERCASE={
	A: /[ÀÁÂÃÅĀĂĄ]/g,
	Ae: /[ÄÆ]/g,
	B: /[Ḃ]/g,
	C: /[ÇĆĈĊČ]/g,
	D: /[ĎĐḊ]/g,
	Dh: /[Ð]/g,
	E: /[ÈÉÊËĒĔĖĘĚ]/g,
	F: /[ƑḞ]/g,
	G: /[ĜĞĠĢ]/g,
	H: /[ĤĦ]/g,
	I: /[ÌÍÎÏĨĪĮ]/g,
	J: /[Ĵ]/g,
	K: /[Ķ]/g,
	L: /[ĹĻĽŁ]/g,
	M: /[Ṁ]/g,
	N: /[ÑŃŅŇ]/g,
	O: /[ÒÓÔÕØŌŐƠ]/g,
	Oe: /[Ö]/g,
	P: /[Ṗ]/g,
	R: /[ŔŖŘŚ]/g,
	S: /[ŜŞŠȘṠ]/g,
	T: /[ŢŤŦȚṪ]/g,
	Th: /[Þ]/g,
	U: /[ÙÚÛŨŪŬŮŰŲƯ]/g,
	Ue: /[Ü]/g,
	W: /[ŴẀẂẄ]/g,
	Y: /[ÝŶŸỲ]/g,
	Z: /[ŹŻŽ]/g
};
/**
 * Remove accents from (latin) characters. Most of the time this method can
 * be used to make a string 7bit ASCII, but fails when non-latin characters
 * (like arabic, cyrilic or chinese) are used. To be sure, see {@link String#isASCII}.
 *
 * @param {number} n (optional) 0: lower- and uppercase (default), -1: lowercase only, 1: uppercase
 * @returns {string} A new string with the accents removed.
 */
String.prototype.deaccent=function(n) {
  n=+n||0; //force number, when error, use 0 as default
  var res=""+this;
  if(n<=0) { //lowercase
    for(var letter in ACCENT_LOWERCASE) {
      var rx=ACCENT_LOWERCASE[letter];
      if(rx.test(res)) res=res.replace(rx,letter);
    }
  }
  if(n>=0) { //uppercase
    for(var letter in ACCENT_UPPERCASE) {
      var rx=ACCENT_UPPERCASE[letter];
      if(rx.test(res)) res=res.replace(rx,letter);
    }
  }
  return res;
}
/**
 * Check if a string contains only ASCII characters.
 *
 * @returns {boolean} true when 100% 7bit ASCII, false otherwise.
 */
String.prototype.isASCII=function() {
  for(var i=0; i<this.length; i++) {
    if(this.charCodeAt(i)>127) return false;
  }
  return true;
}

$(document).ready(function(){
    var copyUrl = function(){
        var val = $('input.title').attr('value');

        val = val.toLowerCase();
        val = val.deaccent();
        val = val.replace(/\s+/g, '-');
        val = val.replace(/[^a-zA-Z0-9-_]/g, '-');
        $('input.url').attr('value', val);
    };
    $('input.title').keyup(copyUrl);
});