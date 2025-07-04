/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License and is not open source.
 * Each license that you purchased is only available for 1 website only.
 * You can't distribute, modify or sell this code.
 * If you want to use this file on more websites, you need to purchase additional licenses.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * If you need help please contact <attechteams@gmail.com>
 *
 * @author    Alpha Tech <attechteams@gmail.com>
 * @copyright 2022 Alpha Tech
 * @license   opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

(() => {
    var t = {
        35691: (t, e, r) => {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            var n, i = (n = r(99663)) && n.__esModule ? n : {default: n};
            e.default = function t(e) {
                (0, i.default)(this, t), this.message = e, this.name = "LocalizationException"
            }
        }, 37210: (t, e, r) => {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            var n = l(r(85315)), i = l(r(88902)), o = l(r(12424)), u = l(r(99663)), a = l(r(22600)), s = l(r(76694)),
                f = l(r(91598)), c = l(r(58182));

            function l(t) {
                return t && t.__esModule ? t : {default: t}
            }

            var p = r(91658), y = function () {
                function t(e) {
                    (0, u.default)(this, t), this.numberSpecification = e
                }

                return (0, a.default)(t, [{
                    key: "format", value: function (t, e) {
                        void 0 !== e && (this.numberSpecification = e);
                        var r = Math.abs(t).toFixed(this.numberSpecification.getMaxFractionDigits()),
                            n = this.extractMajorMinorDigits(r), i = (0, o.default)(n, 2), u = i[0], a = i[1],
                            s = u = this.splitMajorGroups(u);
                        (a = this.adjustMinorDigitsZeroes(a)) && (s += "." + a);
                        var f = this.getCldrPattern(t < 0);
                        return s = this.addPlaceholders(s, f), s = this.replaceSymbols(s), this.performSpecificReplacements(s)
                    }
                }, {
                    key: "extractMajorMinorDigits", value: function (t) {
                        var e = t.toString().split(".");
                        return [e[0], void 0 === e[1] ? "" : e[1]]
                    }
                }, {
                    key: "splitMajorGroups", value: function (t) {
                        if (!this.numberSpecification.isGroupingUsed()) return t;
                        var e = t.split("").reverse(), r = [];
                        for (r.push(e.splice(0, this.numberSpecification.getPrimaryGroupSize())); e.length;) r.push(e.splice(0, this.numberSpecification.getSecondaryGroupSize()));
                        r = r.reverse();
                        var n = [];
                        return r.forEach((function (t) {
                            n.push(t.reverse().join(""))
                        })), n.join(",")
                    }
                }, {
                    key: "adjustMinorDigitsZeroes", value: function (t) {
                        var e = t;
                        return e.length > this.numberSpecification.getMaxFractionDigits() && (e = e.replace(/0+$/, "")), e.length < this.numberSpecification.getMinFractionDigits() && (e = e.padEnd(this.numberSpecification.getMinFractionDigits(), "0")), e
                    }
                }, {
                    key: "getCldrPattern", value: function (t) {
                        return t ? this.numberSpecification.getNegativePattern() : this.numberSpecification.getPositivePattern()
                    }
                }, {
                    key: "replaceSymbols", value: function (t) {
                        var e = this.numberSpecification.getSymbol(), r = {};
                        return r["."] = e.getDecimal(), r[","] = e.getGroup(), r["-"] = e.getMinusSign(), r["%"] = e.getPercentSign(), r["+"] = e.getPlusSign(), this.strtr(t, r)
                    }
                }, {
                    key: "strtr", value: function (t, e) {
                        var r = (0, i.default)(e).map(p);
                        return t.split(RegExp("(" + r.join("|") + ")")).map((function (t) {
                            return e[t] || t
                        })).join("")
                    }
                }, {
                    key: "addPlaceholders", value: function (t, e) {
                        return e.replace(/#?(,#+)*0(\.[0#]+)*/, t)
                    }
                }, {
                    key: "performSpecificReplacements", value: function (t) {
                        return this.numberSpecification instanceof f.default ? t.split("¤").join(this.numberSpecification.getCurrencySymbol()) : t
                    }
                }], [{
                    key: "build", value: function (e) {
                        var r;
                        return r = void 0 !== e.numberSymbols ? new (Function.prototype.bind.apply(s.default, [null].concat((0, n.default)(e.numberSymbols)))) : new (Function.prototype.bind.apply(s.default, [null].concat((0, n.default)(e.symbol)))), new t(e.currencySymbol ? new f.default(e.positivePattern, e.negativePattern, r, parseInt(e.maxFractionDigits, 10), parseInt(e.minFractionDigits, 10), e.groupingUsed, e.primaryGroupSize, e.secondaryGroupSize, e.currencySymbol, e.currencyCode) : new c.default(e.positivePattern, e.negativePattern, r, parseInt(e.maxFractionDigits, 10), parseInt(e.minFractionDigits, 10), e.groupingUsed, e.primaryGroupSize, e.secondaryGroupSize))
                    }
                }]), t
            }();
            e.default = y
        }, 76694: (t, e, r) => {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            var n = u(r(99663)), i = u(r(22600)), o = u(r(35691));

            function u(t) {
                return t && t.__esModule ? t : {default: t}
            }

            var a = function () {
                function t(e, r, i, o, u, a, s, f, c, l, p) {
                    (0, n.default)(this, t), this.decimal = e, this.group = r, this.list = i, this.percentSign = o, this.minusSign = u, this.plusSign = a, this.exponential = s, this.superscriptingExponent = f, this.perMille = c, this.infinity = l, this.nan = p, this.validateData()
                }

                return (0, i.default)(t, [{
                    key: "getDecimal", value: function () {
                        return this.decimal
                    }
                }, {
                    key: "getGroup", value: function () {
                        return this.group
                    }
                }, {
                    key: "getList", value: function () {
                        return this.list
                    }
                }, {
                    key: "getPercentSign", value: function () {
                        return this.percentSign
                    }
                }, {
                    key: "getMinusSign", value: function () {
                        return this.minusSign
                    }
                }, {
                    key: "getPlusSign", value: function () {
                        return this.plusSign
                    }
                }, {
                    key: "getExponential", value: function () {
                        return this.exponential
                    }
                }, {
                    key: "getSuperscriptingExponent", value: function () {
                        return this.superscriptingExponent
                    }
                }, {
                    key: "getPerMille", value: function () {
                        return this.perMille
                    }
                }, {
                    key: "getInfinity", value: function () {
                        return this.infinity
                    }
                }, {
                    key: "getNan", value: function () {
                        return this.nan
                    }
                }, {
                    key: "validateData", value: function () {
                        if (!this.decimal || "string" != typeof this.decimal) throw new o.default("Invalid decimal");
                        if (!this.group || "string" != typeof this.group) throw new o.default("Invalid group");
                        if (!this.list || "string" != typeof this.list) throw new o.default("Invalid symbol list");
                        if (!this.percentSign || "string" != typeof this.percentSign) throw new o.default("Invalid percentSign");
                        if (!this.minusSign || "string" != typeof this.minusSign) throw new o.default("Invalid minusSign");
                        if (!this.plusSign || "string" != typeof this.plusSign) throw new o.default("Invalid plusSign");
                        if (!this.exponential || "string" != typeof this.exponential) throw new o.default("Invalid exponential");
                        if (!this.superscriptingExponent || "string" != typeof this.superscriptingExponent) throw new o.default("Invalid superscriptingExponent");
                        if (!this.perMille || "string" != typeof this.perMille) throw new o.default("Invalid perMille");
                        if (!this.infinity || "string" != typeof this.infinity) throw new o.default("Invalid infinity");
                        if (!this.nan || "string" != typeof this.nan) throw new o.default("Invalid nan")
                    }
                }]), t
            }();
            e.default = a
        }, 58182: (t, e, r) => {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            var n = a(r(99663)), i = a(r(22600)), o = a(r(35691)), u = a(r(76694));

            function a(t) {
                return t && t.__esModule ? t : {default: t}
            }

            var s = function () {
                function t(e, r, i, a, s, f, c, l) {
                    if ((0, n.default)(this, t), this.positivePattern = e, this.negativePattern = r, this.symbol = i, this.maxFractionDigits = a, this.minFractionDigits = a < s ? a : s, this.groupingUsed = f, this.primaryGroupSize = c, this.secondaryGroupSize = l, !this.positivePattern || "string" != typeof this.positivePattern) throw new o.default("Invalid positivePattern");
                    if (!this.negativePattern || "string" != typeof this.negativePattern) throw new o.default("Invalid negativePattern");
                    if (!(this.symbol && this.symbol instanceof u.default)) throw new o.default("Invalid symbol");
                    if ("number" != typeof this.maxFractionDigits) throw new o.default("Invalid maxFractionDigits");
                    if ("number" != typeof this.minFractionDigits) throw new o.default("Invalid minFractionDigits");
                    if ("boolean" != typeof this.groupingUsed) throw new o.default("Invalid groupingUsed");
                    if ("number" != typeof this.primaryGroupSize) throw new o.default("Invalid primaryGroupSize");
                    if ("number" != typeof this.secondaryGroupSize) throw new o.default("Invalid secondaryGroupSize")
                }

                return (0, i.default)(t, [{
                    key: "getSymbol", value: function () {
                        return this.symbol
                    }
                }, {
                    key: "getPositivePattern", value: function () {
                        return this.positivePattern
                    }
                }, {
                    key: "getNegativePattern", value: function () {
                        return this.negativePattern
                    }
                }, {
                    key: "getMaxFractionDigits", value: function () {
                        return this.maxFractionDigits
                    }
                }, {
                    key: "getMinFractionDigits", value: function () {
                        return this.minFractionDigits
                    }
                }, {
                    key: "isGroupingUsed", value: function () {
                        return this.groupingUsed
                    }
                }, {
                    key: "getPrimaryGroupSize", value: function () {
                        return this.primaryGroupSize
                    }
                }, {
                    key: "getSecondaryGroupSize", value: function () {
                        return this.secondaryGroupSize
                    }
                }]), t
            }();
            e.default = s
        }, 91598: (t, e, r) => {
            "use strict";
            Object.defineProperty(e, "__esModule", {value: !0});
            var n = f(r(85105)), i = f(r(99663)), o = f(r(22600)), u = f(r(49135)), a = f(r(93196)), s = f(r(35691));

            function f(t) {
                return t && t.__esModule ? t : {default: t}
            }

            var c = function (t) {
                function e(t, r, o, a, f, c, l, p, y, d) {
                    (0, i.default)(this, e);
                    var v = (0, u.default)(this, (e.__proto__ || (0, n.default)(e)).call(this, t, r, o, a, f, c, l, p));
                    if (v.currencySymbol = y, v.currencyCode = d, !v.currencySymbol || "string" != typeof v.currencySymbol) throw new s.default("Invalid currencySymbol");
                    if (!v.currencyCode || "string" != typeof v.currencyCode) throw new s.default("Invalid currencyCode");
                    return v
                }

                return (0, a.default)(e, t), (0, o.default)(e, [{
                    key: "getCurrencySymbol", value: function () {
                        return this.currencySymbol
                    }
                }, {
                    key: "getCurrencyCode", value: function () {
                        return this.currencyCode
                    }
                }], [{
                    key: "getCurrencyDisplay", value: function () {
                        return "symbol"
                    }
                }]), e
            }(f(r(58182)).default);
            e.default = c
        }, 24043: (t, e, r) => {
            t.exports = {default: r(47185), __esModule: !0}
        }, 26378: (t, e, r) => {
            t.exports = {default: r(3597), __esModule: !0}
        }, 40863: (t, e, r) => {
            t.exports = {default: r(21035), __esModule: !0}
        }, 85861: (t, e, r) => {
            t.exports = {default: r(45627), __esModule: !0}
        }, 32242: (t, e, r) => {
            t.exports = {default: r(33391), __esModule: !0}
        }, 85105: (t, e, r) => {
            t.exports = {default: r(30381), __esModule: !0}
        }, 88902: (t, e, r) => {
            t.exports = {default: r(98613), __esModule: !0}
        }, 85345: (t, e, r) => {
            t.exports = {default: r(70433), __esModule: !0}
        }, 93516: (t, e, r) => {
            t.exports = {default: r(80025), __esModule: !0}
        }, 64275: (t, e, r) => {
            t.exports = {default: r(52392), __esModule: !0}
        }, 99663: (t, e) => {
            "use strict";
            e.__esModule = !0, e.default = function (t, e) {
                if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
            }
        }, 22600: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n, i = (n = r(32242)) && n.__esModule ? n : {default: n};
            e.default = function () {
                function t(t, e) {
                    for (var r = 0; r < e.length; r++) {
                        var n = e[r];
                        n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), (0, i.default)(t, n.key, n)
                    }
                }

                return function (e, r, n) {
                    return r && t(e.prototype, r), n && t(e, n), e
                }
            }()
        }, 93196: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n = u(r(85345)), i = u(r(85861)), o = u(r(72444));

            function u(t) {
                return t && t.__esModule ? t : {default: t}
            }

            e.default = function (t, e) {
                if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function, not " + (void 0 === e ? "undefined" : (0, o.default)(e)));
                t.prototype = (0, i.default)(e && e.prototype, {
                    constructor: {
                        value: t,
                        enumerable: !1,
                        writable: !0,
                        configurable: !0
                    }
                }), e && (n.default ? (0, n.default)(t, e) : t.__proto__ = e)
            }
        }, 49135: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n, i = (n = r(72444)) && n.__esModule ? n : {default: n};
            e.default = function (t, e) {
                if (!t) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
                return !e || "object" !== (void 0 === e ? "undefined" : (0, i.default)(e)) && "function" != typeof e ? t : e
            }
        }, 12424: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n = o(r(40863)), i = o(r(26378));

            function o(t) {
                return t && t.__esModule ? t : {default: t}
            }

            e.default = function (t, e) {
                if (Array.isArray(t)) return t;
                if ((0, n.default)(Object(t))) return function (t, e) {
                    var r = [], n = !0, o = !1, u = void 0;
                    try {
                        for (var a, s = (0, i.default)(t); !(n = (a = s.next()).done) && (r.push(a.value), !e || r.length !== e); n = !0) ;
                    } catch (t) {
                        o = !0, u = t
                    } finally {
                        try {
                            !n && s.return && s.return()
                        } finally {
                            if (o) throw u
                        }
                    }
                    return r
                }(t, e);
                throw new TypeError("Invalid attempt to destructure non-iterable instance")
            }
        }, 85315: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n, i = (n = r(24043)) && n.__esModule ? n : {default: n};
            e.default = function (t) {
                if (Array.isArray(t)) {
                    for (var e = 0, r = Array(t.length); e < t.length; e++) r[e] = t[e];
                    return r
                }
                return (0, i.default)(t)
            }
        }, 72444: (t, e, r) => {
            "use strict";
            e.__esModule = !0;
            var n = u(r(64275)), i = u(r(93516)),
                o = "function" == typeof i.default && "symbol" == typeof n.default ? function (t) {
                    return typeof t
                } : function (t) {
                    return t && "function" == typeof i.default && t.constructor === i.default && t !== i.default.prototype ? "symbol" : typeof t
                };

            function u(t) {
                return t && t.__esModule ? t : {default: t}
            }

            e.default = "function" == typeof i.default && "symbol" === o(n.default) ? function (t) {
                return void 0 === t ? "undefined" : o(t)
            } : function (t) {
                return t && "function" == typeof i.default && t.constructor === i.default && t !== i.default.prototype ? "symbol" : void 0 === t ? "undefined" : o(t)
            }
        }, 47185: (t, e, r) => {
            r(91867), r(2586), t.exports = r(34579).Array.from
        }, 3597: (t, e, r) => {
            r(73871), r(91867), t.exports = r(46459)
        }, 21035: (t, e, r) => {
            r(73871), r(91867), t.exports = r(89553)
        }, 45627: (t, e, r) => {
            r(86760);
            var n = r(34579).Object;
            t.exports = function (t, e) {
                return n.create(t, e)
            }
        }, 33391: (t, e, r) => {
            r(31477);
            var n = r(34579).Object;
            t.exports = function (t, e, r) {
                return n.defineProperty(t, e, r)
            }
        }, 30381: (t, e, r) => {
            r(77220), t.exports = r(34579).Object.getPrototypeOf
        }, 98613: (t, e, r) => {
            r(40961), t.exports = r(34579).Object.keys
        }, 70433: (t, e, r) => {
            r(59349), t.exports = r(34579).Object.setPrototypeOf
        }, 80025: (t, e, r) => {
            r(46840), r(94058), r(8174), r(36461), t.exports = r(34579).Symbol
        }, 52392: (t, e, r) => {
            r(91867), r(73871), t.exports = r(25103).f("iterator")
        }, 85663: t => {
            t.exports = function (t) {
                if ("function" != typeof t) throw TypeError(t + " is not a function!");
                return t
            }
        }, 79003: t => {
            t.exports = function () {
            }
        }, 12159: (t, e, r) => {
            var n = r(36727);
            t.exports = function (t) {
                if (!n(t)) throw TypeError(t + " is not an object!");
                return t
            }
        }, 57428: (t, e, r) => {
            var n = r(7932), i = r(78728), o = r(16531);
            t.exports = function (t) {
                return function (e, r, u) {
                    var a, s = n(e), f = i(s.length), c = o(u, f);
                    if (t && r != r) {
                        for (; f > c;) if ((a = s[c++]) != a) return !0
                    } else for (; f > c; c++) if ((t || c in s) && s[c] === r) return t || c || 0;
                    return !t && -1
                }
            }
        }, 14677: (t, e, r) => {
            var n = r(32894), i = r(22939)("toStringTag"), o = "Arguments" == n(function () {
                return arguments
            }());
            t.exports = function (t) {
                var e, r, u;
                return void 0 === t ? "Undefined" : null === t ? "Null" : "string" == typeof (r = function (t, e) {
                    try {
                        return t[e]
                    } catch (t) {
                    }
                }(e = Object(t), i)) ? r : o ? n(e) : "Object" == (u = n(e)) && "function" == typeof e.callee ? "Arguments" : u
            }
        }, 32894: t => {
            var e = {}.toString;
            t.exports = function (t) {
                return e.call(t).slice(8, -1)
            }
        }, 34579: t => {
            var e = t.exports = {version: "2.6.11"};
            "number" == typeof __e && (__e = e)
        }, 52445: (t, e, r) => {
            "use strict";
            var n = r(4743), i = r(83101);
            t.exports = function (t, e, r) {
                e in t ? n.f(t, e, i(0, r)) : t[e] = r
            }
        }, 19216: (t, e, r) => {
            var n = r(85663);
            t.exports = function (t, e, r) {
                if (n(t), void 0 === e) return t;
                switch (r) {
                    case 1:
                        return function (r) {
                            return t.call(e, r)
                        };
                    case 2:
                        return function (r, n) {
                            return t.call(e, r, n)
                        };
                    case 3:
                        return function (r, n, i) {
                            return t.call(e, r, n, i)
                        }
                }
                return function () {
                    return t.apply(e, arguments)
                }
            }
        }, 8333: t => {
            t.exports = function (t) {
                if (null == t) throw TypeError("Can't call method on  " + t);
                return t
            }
        }, 89666: (t, e, r) => {
            t.exports = !r(7929)((function () {
                return 7 != Object.defineProperty({}, "a", {
                    get: function () {
                        return 7
                    }
                }).a
            }))
        }, 97467: (t, e, r) => {
            var n = r(36727), i = r(33938).document, o = n(i) && n(i.createElement);
            t.exports = function (t) {
                return o ? i.createElement(t) : {}
            }
        }, 73338: t => {
            t.exports = "constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf".split(",")
        }, 70337: (t, e, r) => {
            var n = r(46162), i = r(48195), o = r(86274);
            t.exports = function (t) {
                var e = n(t), r = i.f;
                if (r) for (var u, a = r(t), s = o.f, f = 0; a.length > f;) s.call(t, u = a[f++]) && e.push(u);
                return e
            }
        }, 83856: (t, e, r) => {
            var n = r(33938), i = r(34579), o = r(19216), u = r(41818), a = r(27069), s = function (t, e, r) {
                var f, c, l, p = t & s.F, y = t & s.G, d = t & s.S, v = t & s.P, h = t & s.B, g = t & s.W,
                    b = y ? i : i[e] || (i[e] = {}), m = b.prototype, S = y ? n : d ? n[e] : (n[e] || {}).prototype;
                for (f in y && (r = e), r) (c = !p && S && void 0 !== S[f]) && a(b, f) || (l = c ? S[f] : r[f], b[f] = y && "function" != typeof S[f] ? r[f] : h && c ? o(l, n) : g && S[f] == l ? function (t) {
                    var e = function (e, r, n) {
                        if (this instanceof t) {
                            switch (arguments.length) {
                                case 0:
                                    return new t;
                                case 1:
                                    return new t(e);
                                case 2:
                                    return new t(e, r)
                            }
                            return new t(e, r, n)
                        }
                        return t.apply(this, arguments)
                    };
                    return e.prototype = t.prototype, e
                }(l) : v && "function" == typeof l ? o(Function.call, l) : l, v && ((b.virtual || (b.virtual = {}))[f] = l, t & s.R && m && !m[f] && u(m, f, l)))
            };
            s.F = 1, s.G = 2, s.S = 4, s.P = 8, s.B = 16, s.W = 32, s.U = 64, s.R = 128, t.exports = s
        }, 7929: t => {
            t.exports = function (t) {
                try {
                    return !!t()
                } catch (t) {
                    return !0
                }
            }
        }, 33938: t => {
            var e = t.exports = "undefined" != typeof window && window.Math == Math ? window : "undefined" != typeof self && self.Math == Math ? self : Function("return this")();
            "number" == typeof __g && (__g = e)
        }, 27069: t => {
            var e = {}.hasOwnProperty;
            t.exports = function (t, r) {
                return e.call(t, r)
            }
        }, 41818: (t, e, r) => {
            var n = r(4743), i = r(83101);
            t.exports = r(89666) ? function (t, e, r) {
                return n.f(t, e, i(1, r))
            } : function (t, e, r) {
                return t[e] = r, t
            }
        }, 54881: (t, e, r) => {
            var n = r(33938).document;
            t.exports = n && n.documentElement
        }, 33758: (t, e, r) => {
            t.exports = !r(89666) && !r(7929)((function () {
                return 7 != Object.defineProperty(r(97467)("div"), "a", {
                    get: function () {
                        return 7
                    }
                }).a
            }))
        }, 50799: (t, e, r) => {
            var n = r(32894);
            t.exports = Object("z").propertyIsEnumerable(0) ? Object : function (t) {
                return "String" == n(t) ? t.split("") : Object(t)
            }
        }, 45991: (t, e, r) => {
            var n = r(15449), i = r(22939)("iterator"), o = Array.prototype;
            t.exports = function (t) {
                return void 0 !== t && (n.Array === t || o[i] === t)
            }
        }, 71421: (t, e, r) => {
            var n = r(32894);
            t.exports = Array.isArray || function (t) {
                return "Array" == n(t)
            }
        }, 36727: t => {
            t.exports = function (t) {
                return "object" == typeof t ? null !== t : "function" == typeof t
            }
        }, 95602: (t, e, r) => {
            var n = r(12159);
            t.exports = function (t, e, r, i) {
                try {
                    return i ? e(n(r)[0], r[1]) : e(r)
                } catch (e) {
                    var o = t.return;
                    throw void 0 !== o && n(o.call(t)), e
                }
            }
        }, 33945: (t, e, r) => {
            "use strict";
            var n = r(98989), i = r(83101), o = r(25378), u = {};
            r(41818)(u, r(22939)("iterator"), (function () {
                return this
            })), t.exports = function (t, e, r) {
                t.prototype = n(u, {next: i(1, r)}), o(t, e + " Iterator")
            }
        }, 45700: (t, e, r) => {
            "use strict";
            var n = r(16227), i = r(83856), o = r(57470), u = r(41818), a = r(15449), s = r(33945), f = r(25378),
                c = r(95089), l = r(22939)("iterator"), p = !([].keys && "next" in [].keys()), y = "keys", d = "values",
                v = function () {
                    return this
                };
            t.exports = function (t, e, r, h, g, b, m) {
                s(r, e, h);
                var S, x, _, w = function (t) {
                        if (!p && t in M) return M[t];
                        switch (t) {
                            case y:
                            case d:
                                return function () {
                                    return new r(this, t)
                                }
                        }
                        return function () {
                            return new r(this, t)
                        }
                    }, O = e + " Iterator", P = g == d, j = !1, M = t.prototype, k = M[l] || M["@@iterator"] || g && M[g],
                    E = k || w(g), F = g ? P ? w("entries") : E : void 0, I = "Array" == e && M.entries || k;
                if (I && (_ = c(I.call(new t))) !== Object.prototype && _.next && (f(_, O, !0), n || "function" == typeof _[l] || u(_, l, v)), P && k && k.name !== d && (j = !0, E = function () {
                    return k.call(this)
                }), n && !m || !p && !j && M[l] || u(M, l, E), a[e] = E, a[O] = v, g) if (S = {
                    values: P ? E : w(d),
                    keys: b ? E : w(y),
                    entries: F
                }, m) for (x in S) x in M || o(M, x, S[x]); else i(i.P + i.F * (p || j), e, S);
                return S
            }
        }, 96630: (t, e, r) => {
            var n = r(22939)("iterator"), i = !1;
            try {
                var o = [7][n]();
                o.return = function () {
                    i = !0
                }, Array.from(o, (function () {
                    throw 2
                }))
            } catch (t) {
            }
            t.exports = function (t, e) {
                if (!e && !i) return !1;
                var r = !1;
                try {
                    var o = [7], u = o[n]();
                    u.next = function () {
                        return {done: r = !0}
                    }, o[n] = function () {
                        return u
                    }, t(o)
                } catch (t) {
                }
                return r
            }
        }, 85084: t => {
            t.exports = function (t, e) {
                return {value: e, done: !!t}
            }
        }, 15449: t => {
            t.exports = {}
        }, 16227: t => {
            t.exports = !0
        }, 77177: (t, e, r) => {
            var n = r(65730)("meta"), i = r(36727), o = r(27069), u = r(4743).f, a = 0,
                s = Object.isExtensible || function () {
                    return !0
                }, f = !r(7929)((function () {
                    return s(Object.preventExtensions({}))
                })), c = function (t) {
                    u(t, n, {value: {i: "O" + ++a, w: {}}})
                }, l = t.exports = {
                    KEY: n, NEED: !1, fastKey: function (t, e) {
                        if (!i(t)) return "symbol" == typeof t ? t : ("string" == typeof t ? "S" : "P") + t;
                        if (!o(t, n)) {
                            if (!s(t)) return "F";
                            if (!e) return "E";
                            c(t)
                        }
                        return t[n].i
                    }, getWeak: function (t, e) {
                        if (!o(t, n)) {
                            if (!s(t)) return !0;
                            if (!e) return !1;
                            c(t)
                        }
                        return t[n].w
                    }, onFreeze: function (t) {
                        return f && l.NEED && s(t) && !o(t, n) && c(t), t
                    }
                }
        }, 98989: (t, e, r) => {
            var n = r(12159), i = r(57856), o = r(73338), u = r(58989)("IE_PROTO"), a = function () {
            }, s = function () {
                var t, e = r(97467)("iframe"), n = o.length;
                for (e.style.display = "none", r(54881).appendChild(e), e.src = "javascript:", (t = e.contentWindow.document).open(), t.write("<script>document.F=Object<\/script>"), t.close(), s = t.F; n--;) delete s.prototype[o[n]];
                return s()
            };
            t.exports = Object.create || function (t, e) {
                var r;
                return null !== t ? (a.prototype = n(t), r = new a, a.prototype = null, r[u] = t) : r = s(), void 0 === e ? r : i(r, e)
            }
        }, 4743: (t, e, r) => {
            var n = r(12159), i = r(33758), o = r(33206), u = Object.defineProperty;
            e.f = r(89666) ? Object.defineProperty : function (t, e, r) {
                if (n(t), e = o(e, !0), n(r), i) try {
                    return u(t, e, r)
                } catch (t) {
                }
                if ("get" in r || "set" in r) throw TypeError("Accessors not supported!");
                return "value" in r && (t[e] = r.value), t
            }
        }, 57856: (t, e, r) => {
            var n = r(4743), i = r(12159), o = r(46162);
            t.exports = r(89666) ? Object.defineProperties : function (t, e) {
                i(t);
                for (var r, u = o(e), a = u.length, s = 0; a > s;) n.f(t, r = u[s++], e[r]);
                return t
            }
        }, 76183: (t, e, r) => {
            var n = r(86274), i = r(83101), o = r(7932), u = r(33206), a = r(27069), s = r(33758),
                f = Object.getOwnPropertyDescriptor;
            e.f = r(89666) ? f : function (t, e) {
                if (t = o(t), e = u(e, !0), s) try {
                    return f(t, e)
                } catch (t) {
                }
                if (a(t, e)) return i(!n.f.call(t, e), t[e])
            }
        }, 94368: (t, e, r) => {
            var n = r(7932), i = r(33230).f, o = {}.toString,
                u = "object" == typeof window && window && Object.getOwnPropertyNames ? Object.getOwnPropertyNames(window) : [];
            t.exports.f = function (t) {
                return u && "[object Window]" == o.call(t) ? function (t) {
                    try {
                        return i(t)
                    } catch (t) {
                        return u.slice()
                    }
                }(t) : i(n(t))
            }
        }, 33230: (t, e, r) => {
            var n = r(12963), i = r(73338).concat("length", "prototype");
            e.f = Object.getOwnPropertyNames || function (t) {
                return n(t, i)
            }
        }, 48195: (t, e) => {
            e.f = Object.getOwnPropertySymbols
        }, 95089: (t, e, r) => {
            var n = r(27069), i = r(66530), o = r(58989)("IE_PROTO"), u = Object.prototype;
            t.exports = Object.getPrototypeOf || function (t) {
                return t = i(t), n(t, o) ? t[o] : "function" == typeof t.constructor && t instanceof t.constructor ? t.constructor.prototype : t instanceof Object ? u : null
            }
        }, 12963: (t, e, r) => {
            var n = r(27069), i = r(7932), o = r(57428)(!1), u = r(58989)("IE_PROTO");
            t.exports = function (t, e) {
                var r, a = i(t), s = 0, f = [];
                for (r in a) r != u && n(a, r) && f.push(r);
                for (; e.length > s;) n(a, r = e[s++]) && (~o(f, r) || f.push(r));
                return f
            }
        }, 46162: (t, e, r) => {
            var n = r(12963), i = r(73338);
            t.exports = Object.keys || function (t) {
                return n(t, i)
            }
        }, 86274: (t, e) => {
            e.f = {}.propertyIsEnumerable
        }, 12584: (t, e, r) => {
            var n = r(83856), i = r(34579), o = r(7929);
            t.exports = function (t, e) {
                var r = (i.Object || {})[t] || Object[t], u = {};
                u[t] = e(r), n(n.S + n.F * o((function () {
                    r(1)
                })), "Object", u)
            }
        }, 83101: t => {
            t.exports = function (t, e) {
                return {enumerable: !(1 & t), configurable: !(2 & t), writable: !(4 & t), value: e}
            }
        }, 57470: (t, e, r) => {
            t.exports = r(41818)
        }, 62906: (t, e, r) => {
            var n = r(36727), i = r(12159), o = function (t, e) {
                if (i(t), !n(e) && null !== e) throw TypeError(e + ": can't set as prototype!")
            };
            t.exports = {
                set: Object.setPrototypeOf || ("__proto__" in {} ? function (t, e, n) {
                    try {
                        (n = r(19216)(Function.call, r(76183).f(Object.prototype, "__proto__").set, 2))(t, []), e = !(t instanceof Array)
                    } catch (t) {
                        e = !0
                    }
                    return function (t, r) {
                        return o(t, r), e ? t.__proto__ = r : n(t, r), t
                    }
                }({}, !1) : void 0), check: o
            }
        }, 25378: (t, e, r) => {
            var n = r(4743).f, i = r(27069), o = r(22939)("toStringTag");
            t.exports = function (t, e, r) {
                t && !i(t = r ? t : t.prototype, o) && n(t, o, {configurable: !0, value: e})
            }
        }, 58989: (t, e, r) => {
            var n = r(20250)("keys"), i = r(65730);
            t.exports = function (t) {
                return n[t] || (n[t] = i(t))
            }
        }, 20250: (t, e, r) => {
            var n = r(34579), i = r(33938), o = "__core-js_shared__", u = i[o] || (i[o] = {});
            (t.exports = function (t, e) {
                return u[t] || (u[t] = void 0 !== e ? e : {})
            })("versions", []).push({
                version: n.version,
                mode: r(16227) ? "pure" : "global",
                copyright: "© 2019 Denis Pushkarev (zloirock.ru)"
            })
        }, 90510: (t, e, r) => {
            var n = r(11052), i = r(8333);
            t.exports = function (t) {
                return function (e, r) {
                    var o, u, a = String(i(e)), s = n(r), f = a.length;
                    return s < 0 || s >= f ? t ? "" : void 0 : (o = a.charCodeAt(s)) < 55296 || o > 56319 || s + 1 === f || (u = a.charCodeAt(s + 1)) < 56320 || u > 57343 ? t ? a.charAt(s) : o : t ? a.slice(s, s + 2) : u - 56320 + (o - 55296 << 10) + 65536
                }
            }
        }, 16531: (t, e, r) => {
            var n = r(11052), i = Math.max, o = Math.min;
            t.exports = function (t, e) {
                return (t = n(t)) < 0 ? i(t + e, 0) : o(t, e)
            }
        }, 11052: t => {
            var e = Math.ceil, r = Math.floor;
            t.exports = function (t) {
                return isNaN(t = +t) ? 0 : (t > 0 ? r : e)(t)
            }
        }, 7932: (t, e, r) => {
            var n = r(50799), i = r(8333);
            t.exports = function (t) {
                return n(i(t))
            }
        }, 78728: (t, e, r) => {
            var n = r(11052), i = Math.min;
            t.exports = function (t) {
                return t > 0 ? i(n(t), 9007199254740991) : 0
            }
        }, 66530: (t, e, r) => {
            var n = r(8333);
            t.exports = function (t) {
                return Object(n(t))
            }
        }, 33206: (t, e, r) => {
            var n = r(36727);
            t.exports = function (t, e) {
                if (!n(t)) return t;
                var r, i;
                if (e && "function" == typeof (r = t.toString) && !n(i = r.call(t))) return i;
                if ("function" == typeof (r = t.valueOf) && !n(i = r.call(t))) return i;
                if (!e && "function" == typeof (r = t.toString) && !n(i = r.call(t))) return i;
                throw TypeError("Can't convert object to primitive value")
            }
        }, 65730: t => {
            var e = 0, r = Math.random();
            t.exports = function (t) {
                return "Symbol(".concat(void 0 === t ? "" : t, ")_", (++e + r).toString(36))
            }
        }, 76347: (t, e, r) => {
            var n = r(33938), i = r(34579), o = r(16227), u = r(25103), a = r(4743).f;
            t.exports = function (t) {
                var e = i.Symbol || (i.Symbol = o ? {} : n.Symbol || {});
                "_" == t.charAt(0) || t in e || a(e, t, {value: u.f(t)})
            }
        }, 25103: (t, e, r) => {
            e.f = r(22939)
        }, 22939: (t, e, r) => {
            var n = r(20250)("wks"), i = r(65730), o = r(33938).Symbol, u = "function" == typeof o;
            (t.exports = function (t) {
                return n[t] || (n[t] = u && o[t] || (u ? o : i)("Symbol." + t))
            }).store = n
        }, 83728: (t, e, r) => {
            var n = r(14677), i = r(22939)("iterator"), o = r(15449);
            t.exports = r(34579).getIteratorMethod = function (t) {
                if (null != t) return t[i] || t["@@iterator"] || o[n(t)]
            }
        }, 46459: (t, e, r) => {
            var n = r(12159), i = r(83728);
            t.exports = r(34579).getIterator = function (t) {
                var e = i(t);
                if ("function" != typeof e) throw TypeError(t + " is not iterable!");
                return n(e.call(t))
            }
        }, 89553: (t, e, r) => {
            var n = r(14677), i = r(22939)("iterator"), o = r(15449);
            t.exports = r(34579).isIterable = function (t) {
                var e = Object(t);
                return void 0 !== e[i] || "@@iterator" in e || o.hasOwnProperty(n(e))
            }
        }, 2586: (t, e, r) => {
            "use strict";
            var n = r(19216), i = r(83856), o = r(66530), u = r(95602), a = r(45991), s = r(78728), f = r(52445),
                c = r(83728);
            i(i.S + i.F * !r(96630)((function (t) {
                Array.from(t)
            })), "Array", {
                from: function (t) {
                    var e, r, i, l, p = o(t), y = "function" == typeof this ? this : Array, d = arguments.length,
                        v = d > 1 ? arguments[1] : void 0, h = void 0 !== v, g = 0, b = c(p);
                    if (h && (v = n(v, d > 2 ? arguments[2] : void 0, 2)), null == b || y == Array && a(b)) for (r = new y(e = s(p.length)); e > g; g++) f(r, g, h ? v(p[g], g) : p[g]); else for (l = b.call(p), r = new y; !(i = l.next()).done; g++) f(r, g, h ? u(l, v, [i.value, g], !0) : i.value);
                    return r.length = g, r
                }
            })
        }, 3882: (t, e, r) => {
            "use strict";
            var n = r(79003), i = r(85084), o = r(15449), u = r(7932);
            t.exports = r(45700)(Array, "Array", (function (t, e) {
                this._t = u(t), this._i = 0, this._k = e
            }), (function () {
                var t = this._t, e = this._k, r = this._i++;
                return !t || r >= t.length ? (this._t = void 0, i(1)) : i(0, "keys" == e ? r : "values" == e ? t[r] : [r, t[r]])
            }), "values"), o.Arguments = o.Array, n("keys"), n("values"), n("entries")
        }, 86760: (t, e, r) => {
            var n = r(83856);
            n(n.S, "Object", {create: r(98989)})
        }, 31477: (t, e, r) => {
            var n = r(83856);
            n(n.S + n.F * !r(89666), "Object", {defineProperty: r(4743).f})
        }, 77220: (t, e, r) => {
            var n = r(66530), i = r(95089);
            r(12584)("getPrototypeOf", (function () {
                return function (t) {
                    return i(n(t))
                }
            }))
        }, 40961: (t, e, r) => {
            var n = r(66530), i = r(46162);
            r(12584)("keys", (function () {
                return function (t) {
                    return i(n(t))
                }
            }))
        }, 59349: (t, e, r) => {
            var n = r(83856);
            n(n.S, "Object", {setPrototypeOf: r(62906).set})
        }, 94058: () => {
        }, 91867: (t, e, r) => {
            "use strict";
            var n = r(90510)(!0);
            r(45700)(String, "String", (function (t) {
                this._t = String(t), this._i = 0
            }), (function () {
                var t, e = this._t, r = this._i;
                return r >= e.length ? {value: void 0, done: !0} : (t = n(e, r), this._i += t.length, {
                    value: t,
                    done: !1
                })
            }))
        }, 46840: (t, e, r) => {
            "use strict";
            var n = r(33938), i = r(27069), o = r(89666), u = r(83856), a = r(57470), s = r(77177).KEY, f = r(7929),
                c = r(20250), l = r(25378), p = r(65730), y = r(22939), d = r(25103), v = r(76347), h = r(70337),
                g = r(71421), b = r(12159), m = r(36727), S = r(66530), x = r(7932), _ = r(33206), w = r(83101),
                O = r(98989), P = r(94368), j = r(76183), M = r(48195), k = r(4743), E = r(46162), F = j.f, I = k.f,
                D = P.f, T = n.Symbol, A = n.JSON, G = A && A.stringify, L = y("_hidden"), C = y("toPrimitive"),
                N = {}.propertyIsEnumerable, z = c("symbol-registry"), R = c("symbols"), U = c("op-symbols"),
                V = Object.prototype, W = "function" == typeof T && !!M.f, H = n.QObject,
                J = !H || !H.prototype || !H.prototype.findChild, B = o && f((function () {
                    return 7 != O(I({}, "a", {
                        get: function () {
                            return I(this, "a", {value: 7}).a
                        }
                    })).a
                })) ? function (t, e, r) {
                    var n = F(V, e);
                    n && delete V[e], I(t, e, r), n && t !== V && I(V, e, n)
                } : I, K = function (t) {
                    var e = R[t] = O(T.prototype);
                    return e._k = t, e
                }, $ = W && "symbol" == typeof T.iterator ? function (t) {
                    return "symbol" == typeof t
                } : function (t) {
                    return t instanceof T
                }, Y = function (t, e, r) {
                    return t === V && Y(U, e, r), b(t), e = _(e, !0), b(r), i(R, e) ? (r.enumerable ? (i(t, L) && t[L][e] && (t[L][e] = !1), r = O(r, {enumerable: w(0, !1)})) : (i(t, L) || I(t, L, w(1, {})), t[L][e] = !0), B(t, e, r)) : I(t, e, r)
                }, Z = function (t, e) {
                    b(t);
                    for (var r, n = h(e = x(e)), i = 0, o = n.length; o > i;) Y(t, r = n[i++], e[r]);
                    return t
                }, q = function (t) {
                    var e = N.call(this, t = _(t, !0));
                    return !(this === V && i(R, t) && !i(U, t)) && (!(e || !i(this, t) || !i(R, t) || i(this, L) && this[L][t]) || e)
                }, Q = function (t, e) {
                    if (t = x(t), e = _(e, !0), t !== V || !i(R, e) || i(U, e)) {
                        var r = F(t, e);
                        return !r || !i(R, e) || i(t, L) && t[L][e] || (r.enumerable = !0), r
                    }
                }, X = function (t) {
                    for (var e, r = D(x(t)), n = [], o = 0; r.length > o;) i(R, e = r[o++]) || e == L || e == s || n.push(e);
                    return n
                }, tt = function (t) {
                    for (var e, r = t === V, n = D(r ? U : x(t)), o = [], u = 0; n.length > u;) !i(R, e = n[u++]) || r && !i(V, e) || o.push(R[e]);
                    return o
                };
            W || (a((T = function () {
                if (this instanceof T) throw TypeError("Symbol is not a constructor!");
                var t = p(arguments.length > 0 ? arguments[0] : void 0), e = function (r) {
                    this === V && e.call(U, r), i(this, L) && i(this[L], t) && (this[L][t] = !1), B(this, t, w(1, r))
                };
                return o && J && B(V, t, {configurable: !0, set: e}), K(t)
            }).prototype, "toString", (function () {
                return this._k
            })), j.f = Q, k.f = Y, r(33230).f = P.f = X, r(86274).f = q, M.f = tt, o && !r(16227) && a(V, "propertyIsEnumerable", q, !0), d.f = function (t) {
                return K(y(t))
            }), u(u.G + u.W + u.F * !W, {Symbol: T});
            for (var et = "hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables".split(","), rt = 0; et.length > rt;) y(et[rt++]);
            for (var nt = E(y.store), it = 0; nt.length > it;) v(nt[it++]);
            u(u.S + u.F * !W, "Symbol", {
                for: function (t) {
                    return i(z, t += "") ? z[t] : z[t] = T(t)
                }, keyFor: function (t) {
                    if (!$(t)) throw TypeError(t + " is not a symbol!");
                    for (var e in z) if (z[e] === t) return e
                }, useSetter: function () {
                    J = !0
                }, useSimple: function () {
                    J = !1
                }
            }), u(u.S + u.F * !W, "Object", {
                create: function (t, e) {
                    return void 0 === e ? O(t) : Z(O(t), e)
                },
                defineProperty: Y,
                defineProperties: Z,
                getOwnPropertyDescriptor: Q,
                getOwnPropertyNames: X,
                getOwnPropertySymbols: tt
            });
            var ot = f((function () {
                M.f(1)
            }));
            u(u.S + u.F * ot, "Object", {
                getOwnPropertySymbols: function (t) {
                    return M.f(S(t))
                }
            }), A && u(u.S + u.F * (!W || f((function () {
                var t = T();
                return "[null]" != G([t]) || "{}" != G({a: t}) || "{}" != G(Object(t))
            }))), "JSON", {
                stringify: function (t) {
                    for (var e, r, n = [t], i = 1; arguments.length > i;) n.push(arguments[i++]);
                    if (r = e = n[1], (m(e) || void 0 !== t) && !$(t)) return g(e) || (e = function (t, e) {
                        if ("function" == typeof r && (e = r.call(this, t, e)), !$(e)) return e
                    }), n[1] = e, G.apply(A, n)
                }
            }), T.prototype[C] || r(41818)(T.prototype, C, T.prototype.valueOf), l(T, "Symbol"), l(Math, "Math", !0), l(n.JSON, "JSON", !0)
        }, 8174: (t, e, r) => {
            r(76347)("asyncIterator")
        }, 36461: (t, e, r) => {
            r(76347)("observable")
        }, 73871: (t, e, r) => {
            r(3882);
            for (var n = r(33938), i = r(41818), o = r(15449), u = r(22939)("toStringTag"), a = "CSSRuleList,CSSStyleDeclaration,CSSValueList,ClientRectList,DOMRectList,DOMStringList,DOMTokenList,DataTransferItemList,FileList,HTMLAllCollection,HTMLCollection,HTMLFormElement,HTMLSelectElement,MediaList,MimeTypeArray,NamedNodeMap,NodeList,PaintRequestList,Plugin,PluginArray,SVGLengthList,SVGNumberList,SVGPathSegList,SVGPointList,SVGStringList,SVGTransformList,SourceBufferList,StyleSheetList,TextTrackCueList,TextTrackList,TouchList".split(","), s = 0; s < a.length; s++) {
                var f = a[s], c = n[f], l = c && c.prototype;
                l && !l[u] && i(l, u, f), o[f] = o.Array
            }
        }, 91658: (t, e, r) => {
            var n = /[\\^$.*+?()[\]{}|]/g, i = RegExp(n.source),
                o = "object" == typeof r.g && r.g && r.g.Object === Object && r.g,
                u = "object" == typeof self && self && self.Object === Object && self,
                a = o || u || Function("return this")(), s = Object.prototype.toString, f = a.Symbol,
                c = f ? f.prototype : void 0, l = c ? c.toString : void 0;
            t.exports = function (t) {
                var e;
                return (t = null == (e = t) ? "" : function (t) {
                    if ("string" == typeof t) return t;
                    if (function (t) {
                        return "symbol" == typeof t || function (t) {
                            return !!t && "object" == typeof t
                        }(t) && "[object Symbol]" == s.call(t)
                    }(t)) return l ? l.call(t) : "";
                    var e = t + "";
                    return "0" == e && 1 / t == -1 / 0 ? "-0" : e
                }(e)) && i.test(t) ? t.replace(n, "\\$&") : t
            }
        }
    }, e = {};

    function r(n) {
        var i = e[n];
        if (void 0 !== i) return i.exports;
        var o = e[n] = {exports: {}};
        return t[n](o, o.exports, r), o.exports
    }

    r.g = function () {
        if ("object" == typeof globalThis) return globalThis;
        try {
            return this || new Function("return this")()
        } catch (t) {
            if ("object" == typeof window) return window
        }
    }();
    var n = {};
    (() => {
        "use strict";
        var t = n;
        Object.defineProperty(t, "__esModule", {value: !0}), t.NumberSymbol = t.NumberFormatter = t.NumberSpecification = t.PriceSpecification = void 0;
        var e = a(r(37210)), i = a(r(76694)), o = a(r(91598)), u = a(r(58182));

        function a(t) {
            return t && t.__esModule ? t : {default: t}
        }

        t.PriceSpecification = o.default, t.NumberSpecification = u.default, t.NumberFormatter = e.default, t.NumberSymbol = i.default
    })(), window.cldr = n
})();

var currencyFormatter;
var numberFormatter;

var Tools = {

    /**
     * Constructs a float value from an arbitrarily-formatted string.
     * In order to prevent unexpected behavior, make sure that your value has a decimal part.
     * @param {String} value Value to convert to float
     * @param {Boolean} [coerce=false] If true, this function will return 0 instad of NaN if the value cannot be parsed to float
     *
     * @return {Number}
     */
    parseFloatFromString: function(value, coerce) {
        value = String(value).trim();

        if ('' === value) {
            return 0;
        }

        // check if the string can be converted to float as-is
        var parsed = parseFloat(value);
        if (String(parsed) === value) {
            return parsed;
        }

        // replace arabic numbers by latin
        value = value
            // arabic
            .replace(/[\u0660-\u0669]/g, function(d) {
                return d.charCodeAt(0) - 1632;
            })
            // persian
            .replace(/[\u06F0-\u06F9]/g, function(d) {
                return d.charCodeAt(0) - 1776;
            })
        ;

        // remove all non-digit characters
        var split = value.split(/[^\dE-]+/);

        if (1 === split.length) {
            // there's no decimal part
            return parseFloat(value);
        }

        for (var i = 0; i < split.length; i++) {
            if ('' === split[i]) {
                return coerce ? 0 : NaN;
            }
        }

        // use the last part as decimal
        var decimal = split.pop();

        // reconstruct the number using dot as decimal separator
        return parseFloat(split.join('') +  '.' + decimal);
    }
};

/**
 * @deprecated Please use asynchronous formatCurrencyCldr() instead.
 *
 * @param price float The value to format in a price
 * @param currencyFormat Not used anymore
 * @param currencySign Not used anymore
 * @param currencyBlank Not used anymore
 * @returns string a formatted price according to the current locale settings
 */
function formatCurrency(price, currencyFormat, currencySign, currencyBlank)
{
    // if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
    var blank = '';
    price = parseFloat(price.toFixed(10));
    price = ps_round(price, priceDisplayPrecision);
    if (currencyBlank > 0)
        blank = ' ';
    if (currencyFormat == 1)
        return currencySign + blank + formatNumber(price, priceDisplayPrecision, ',', '.');
    if (currencyFormat == 2)
        return (formatNumber(price, priceDisplayPrecision, ' ', ',') + blank + currencySign);
    if (currencyFormat == 3)
        return (currencySign + blank + formatNumber(price, priceDisplayPrecision, '.', ','));
    if (currencyFormat == 4)
        return (formatNumber(price, priceDisplayPrecision, ',', '.') + blank + currencySign);
    if (currencyFormat == 5)
        return (currencySign + blank + formatNumber(price, priceDisplayPrecision, '\'', '.'));
    return price;
}

/**
 * @returns float parsed from a string containing a formatted price
 */
function formatedNumberToFloat(price, currencyFormat, currencySign)
{
    price = price.replace(currencySign, '');
    if (currencyFormat === 1)
        return parseFloat(price.replace(',', '').replace(' ', ''));
    else if (currencyFormat === 2)
        return parseFloat(price.replace(' ', '').replace(',', '.'));
    else if (currencyFormat === 3)
        return parseFloat(price.replace('.', '').replace(' ', '').replace(',', '.'));
    else if (currencyFormat === 4)
        return parseFloat(price.replace(',', '').replace(' ', ''));
    return price;
}

/**
 * This call will load CLDR data to format a number according to the page locale, and then send formatted
 * number into parameter of the callback function. This function is asynchronous as AJAX calls may occur.
 *
 * @param value float The number to format
 * @param callback The function to call with the resulting formatted number as unique parameter
 * @param numberOfDecimal Size of fractionnal part in the number
 */
function formatNumberCldr(value, callback, numberOfDecimal) {
    callback(getNumberFormatter(numberOfDecimal).format(value));
}

/**
 * This call will load CLDR data to format a price according to the page locale, and then send formatted
 * price into parameter of the callback function. This function is asynchronous as AJAX calls may occur.
 *
 * @param price float The price to format
 * @param callback The function to call with the resulting formatted price as unique parameter
 */
function formatCurrencyCldr(price, callback) {
    callback(getCurrencyFormatter().format(price));
}

/**
 * Simple function to generate global NumberFormatter
 * with a price specification
 * based one global currency_specifications
 */
function getCurrencyFormatter() {
    if (currencyFormatter === undefined) {
        currencyFormatter = window.cldr.NumberFormatter.build(currency_specifications);
    }

    return currencyFormatter;
}

/**
 * Simple function to generate global NumberFormatter
 * based one global currency_specifications
 * @param numberOfDecimal Size of fractionnal part in the number
 */
function getNumberFormatter(numberOfDecimal) {
    if (numberFormatter === undefined) {
        numberFormatter = window.cldr.NumberFormatter.build(number_specifications);
    }

    if (numberOfDecimal === undefined) {
        numberOfDecimal = 2;
    }

    numberFormatter.numberSpecification.maxFractionDigits = numberOfDecimal;
    numberFormatter.numberSpecification.minFractionDigits = numberOfDecimal;

    return numberFormatter;
}

function ps_round_helper(value, mode)
{
    // From PHP Math.c
    if (value >= 0.0)
    {
        tmp_value = Math.floor(value + 0.5);
        if ((mode == 3 && value == (-0.5 + tmp_value)) ||
            (mode == 4 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0))) ||
            (mode == 5 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0) - 1.0)))
            tmp_value -= 1.0;
    }
    else
    {
        tmp_value = Math.ceil(value - 0.5);
        if ((mode == 3 && value == (0.5 + tmp_value)) ||
            (mode == 4 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0))) ||
            (mode == 5 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0) + 1.0)))
            tmp_value += 1.0;
    }

    return tmp_value;
}

function ps_log10(value)
{
    return Math.log(value) / Math.LN10;
}

function ps_round_half_up(value, precision)
{
    var mul = Math.pow(10, precision);
    var val = value * mul;

    var next_digit = Math.floor(val * 10) - 10 * Math.floor(val);
    if (next_digit >= 5)
        val = Math.ceil(val);
    else
        val = Math.floor(val);

    return val / mul;
}

function ps_round(value, places)
{
    if (typeof(roundMode) === 'undefined')
        roundMode = 2;
    if (typeof(places) === 'undefined')
        places = 2;

    var method = roundMode;

    if (method === 0)
        return ceilf(value, places);
    else if (method === 1)
        return floorf(value, places);
    else if (method === 2)
        return ps_round_half_up(value, places);
    else if (method == 3 || method == 4 || method == 5)
    {
        // From PHP Math.c
        var precision_places = 14 - Math.floor(ps_log10(Math.abs(value)));
        var f1 = Math.pow(10, Math.abs(places));

        if (precision_places > places && precision_places - places < 15)
        {
            var f2 = Math.pow(10, Math.abs(precision_places));
            if (precision_places >= 0)
                tmp_value = value * f2;
            else
                tmp_value = value / f2;

            tmp_value = ps_round_helper(tmp_value, roundMode);

            /* now correctly move the decimal point */
            f2 = Math.pow(10, Math.abs(places - precision_places));
            /* because places < precision_places */
            tmp_value /= f2;
        }
        else
        {
            /* adjust the value */
            if (places >= 0)
                tmp_value = value * f1;
            else
                tmp_value = value / f1;

            if (Math.abs(tmp_value) >= 1e15)
                return value;
        }

        tmp_value = ps_round_helper(tmp_value, roundMode);
        if (places > 0)
            tmp_value = tmp_value / f1;
        else
            tmp_value = tmp_value * f1;

        return tmp_value;
    }
}

function truncateDecimals(value, decimals)
{
    var numPower = Math.pow(10, decimals);
    var tempNumber = value * numPower;
    var roundedTempNumber = Math.floor(tempNumber);
    return roundedTempNumber / numPower;
}

function ceilf(value, precision)
{
    if (typeof(precision) === 'undefined')
        precision = 0;
    var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
    var tmp = value * precisionFactor;
    var tmp2 = tmp.toString();
    if (tmp2[tmp2.length - 1] === 0)
        return value;
    return Math.ceil(value * precisionFactor) / precisionFactor;
}

function floorf(value, precision)
{
    if (typeof(precision) === 'undefined')
        precision = 0;
    var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
    var tmp = value * precisionFactor;
    var tmp2 = tmp.toString();
    if (tmp2[tmp2.length - 1] === 0)
        return value;
    return Math.floor(value * precisionFactor) / precisionFactor;
}


