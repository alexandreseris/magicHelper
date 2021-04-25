import json
import re
import itertools
import random

with open("./var/scryfallData.json", "r", encoding="utf8") as fh:
    d = json.load(fh)

GENERIC_MANA_TEXT = "GENERIC_MANA"
GENERIC_MANA = "{" + GENERIC_MANA_TEXT + "}"
colorList = ('W', 'U', 'B', 'R', 'G')
standardManaList = ("C",) + colorList
variableManaList = ("X", "Y", "Z")
multiManaGenericManaList = ("2",)
genericManaList = (GENERIC_MANA_TEXT,) + multiManaGenericManaList + variableManaList
notManaSymbol = ("P",)

# extract mana cost from faces and card
# exclusion of mana cost on card level if there is card faces defined (redundant information)

manaResFaces = set([y.get("mana_cost") for x in [x for x in d if x.get("card_faces") is not None] for y in x.get("card_faces") if y.get("mana_cost") is not None and y.get("mana_cost") != ""])
manaRes = set([x.get("mana_cost") for x in d if x.get("mana_cost") is not None and x.get("mana_cost") != "" and x.get("card_faces") is None])
manaRes.update(manaResFaces)

# for each mana cost retrieve earlier:
# exclude every same consecutive mana symbol
# and exclude every mana cost beeing just one symbol
# also replace {\d+} by the same value to reduce number of elements to analyse

manaResUniqConsecutive = set()
for elem in manaRes:
    splitElem = []
    buffer = ""
    for char in elem:
        buffer += char
        if char == "}":
            if re.match("^\{\d+\}$", buffer):
                buffer = GENERIC_MANA
            cleanedSymbol = buffer.replace("{", "").replace("}", "")
            if len(splitElem) == 0 or cleanedSymbol != splitElem[-1]:
                splitElem.append(cleanedSymbol)
            buffer = ""
    if len(splitElem) > 1:
        tupleConvert = tuple(splitElem)
        manaResUniqConsecutive.add(tupleConvert)

# retrieve uniq list of symbol available from current data
uniqSymbols = set([y for x in manaResUniqConsecutive for y in x])


def hasColor(symbol):
    res = False
    for symb in symbol.split("/"):
        if symb in colorList:
            res = True
    return res


def hasStandardMana(symbol):
    res = False
    for symb in symbol.split("/"):
        if symb in standardManaList:
            res = True
    return res


def hasGenericMana(symbol):
    res = False
    for symb in symbol.split("/"):
        if symb in genericManaList:
            res = True
    return res


def hasNotMana(symbol):
    res = False
    for symb in symbol.split("/"):
        if symb in notManaSymbol:
            res = True
    return res


def isMultipleSymbol(symbol):
    return "/" in symbol


symbolDict = {}
for symbol in uniqSymbols:
    symbolDict[symbol] = {
        "color": hasColor(symbol),
        "std_mana": hasStandardMana(symbol),
        "generic_mana": hasGenericMana(symbol),
        "not_mana": hasNotMana(symbol),
        "multiple": isMultipleSymbol(symbol)
    }


def sortSymbols(symbol):
    return (
        symbolDict[symbol]["multiple"],
        symbolDict[symbol]["color"],
        symbolDict[symbol]["std_mana"],
        symbolDict[symbol]["generic_mana"],
        symbolDict[symbol]["not_mana"],
        symbol
    )


def groupby(symbol):
    return (
        symbolDict[symbol]
    )


for group in itertools.groupby(sorted(symbolDict, key=sortSymbols, reverse=True), key=groupby):
    print("GROUP: ", group[0])
    for value in group[1]:
        print("     SYMBOL: ", value)
        for manaCost in manaResUniqConsecutive:
            if value in manaCost:
                print("        MANA COST: ", manaCost)


def manaCostGetterFromTupleGroup(group):
    return [x for x in symbolDict if symbolDict[x] == dict(group)]


# simplify mana cost list to categories in order to determine order between categories
manaCostGroups = set()
for manaCost in manaResUniqConsecutive:
    manaCostGroup = []
    for symbol in manaCost:
        manaCostGroupItem = tuple(symbolDict[symbol].items())
        if len(manaCostGroup) == 0 or manaCostGroup[-1] != manaCostGroupItem:
            manaCostGroup.append(manaCostGroupItem)
    if len(manaCostGroup) > 1:
        manaCostGroups.add(tuple(manaCostGroup))

# print groups with an exemple if needed
for manaGroup in set([y for x in manaCostGroups for y in x]):
    print(manaCostGetterFromTupleGroup(manaGroup)[0], manaGroup)

# print previous calculated data with symbol exemple for readability
for ind, manaCost in enumerate(manaCostGroups):
    print(ind)
    for manaCostElem in manaCost:
        print("    ", manaCostGetterFromTupleGroup(manaCostElem)[0])


"""
"multi_full_color" W/U (('color', True), ('std_mana', True), ('generic_mana', False), ('not_mana', False), ('multiple', True))
"mutli_with_color_and_gen" 2/B (('color', True), ('std_mana', True), ('generic_mana', True), ('not_mana', False), ('multiple', True))
"mutli_with_no_mana" R/P (('color', True), ('std_mana', True), ('generic_mana', False), ('not_mana', True), ('multiple', True))
"generic" X (('color', False), ('std_mana', False), ('generic_mana', True), ('not_mana', False), ('multiple', False))
"colorless" C (('color', False), ('std_mana', True), ('generic_mana', False), ('not_mana', False), ('multiple', False))
"color" U (('color', True), ('std_mana', True), ('generic_mana', False), ('not_mana', False), ('multiple', False))

category order from first to last, and order inside category:
    generic: 'X', 'Y', 'Z', 'GENERIC_MANA'
    mutli_with_no_mana | mutli_with_color_and_gen | multi_full_color => no way to determine with current data, proposition:
        mutli_with_color_and_gen: same as color
        mutli_with_no_mana: same as color
        multi_full_color: it seems it is alpha ordered???? but theres only one instance: https://scryfall.com/card/cmb1/90/evil-boros-charm?utm_source=api
            for easier sort algo, i'll just follow color order
    colorless: 'C'
    color: 'W', 'U', 'B', 'R', 'G'
"""

# python implementation test to validate algo

excpectedOutput = [
    "X",
    "Z",
    "10",
    "1",
    "2/W",
    "2/B",
    "W/P",
    "R/P",
    "W/U",
    "W/B",
    "B/R",
    "C",
    "W",
    "W",
    "G"
]
manaCostExemple = list(excpectedOutput)
random.shuffle(manaCostExemple)


def manaCostSorter(manaCost):
    manaCostDict = dict()
    for symbol in set(manaCost):
        isVariableGenericMana = False
        isNumber = False
        number = None
        isMulti = False
        isMultiWithGen = False
        isMultiWithNoMana = False
        isMultiFullColor = False
        isStandardMana = False
        manaIndex = ()
        if re.search("^\d+$", symbol):
            isNumber = True
            number = -int(symbol)
        if symbol in variableManaList:
            isVariableGenericMana = True
            try:
                manaIndex = (variableManaList.index(symbol),)
            except:
                pass
        if "/" in symbol:
            isMulti = True
            manaIndexList = []
            for subSymb in symbol.split("/"):
                if subSymb in notManaSymbol:
                    isMultiWithNoMana = True
                elif subSymb in multiManaGenericManaList:
                    isMultiWithGen = True
                try:
                    manaIndexList.append(standardManaList.index(subSymb))
                except:
                    pass
            if not isMultiWithNoMana and not isMultiWithGen:
                isMultiFullColor = True
            manaIndex = tuple(manaIndexList)
        if symbol in standardManaList:
            isStandardMana = True
            try:
                manaIndex = (standardManaList.index(symbol), )
            except:
                pass
        manaCostDict[symbol] = (
            not isVariableGenericMana,
            not isNumber,
            number,
            not isMulti,
            not isMultiWithGen,
            not isMultiWithNoMana,
            not isMultiFullColor,
            not isStandardMana,
            manaIndex
        )
        # print(symbol, manaCostDict[symbol])
    return sorted(manaCost, key=lambda x: manaCostDict[x])


outTest = manaCostSorter(manaCostExemple)
if excpectedOutput != outTest:
    print("order failled, model was")
    print(excpectedOutput)
else:
    print("order passed")

print(outTest)
