<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version="1.0">

<xsl:output method="html"
            indent="yes"
            encoding="utf-8"
            omit-xml-declaration="no"
            doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
            doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" />

<xsl:template match='document'>
    <html>
        <head>
            <title>
                <xsl:value-of select='@label' />
            </title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <link type='text/css' rel='stylesheet' href='{@style-file}' />  
        </head>
        <body>
            <div class='body'>
                <xsl:apply-templates select='content' />
            </div>
        </body>
    </html>
</xsl:template>

<xsl:template match='content'>
    <div class='{name()}'>
        <xsl:apply-templates select='field[@class="img"]' />
        <div class='forms'>
            <form class='{name()}' action='?' id='calc_result'>
                <xsl:apply-templates select='field[@class="inputs"]' />
                <div class='button submit'>
                    <input type='submit' value='Рассчитать' />
                </div>
            </form>
            <xsl:if test='not(//checkinput[@*="error"])'>
                <xsl:variable name='filename' select='//profile/@filename' />
                <form class='{name()}' action='./result/{$filename}' id='save_result'>
                    <div class='button save'>
                        <input type='submit' value='Сохранить' />
                    </div>
                </form>
            </xsl:if>
        </div>
        <xsl:apply-templates select='field[@class="result"]' />
    </div>
</xsl:template>

<xsl:template match='field[@class="inputs"]'>
    <h1><xsl:value-of select='@label' /></h1>
    <fieldset class='{@class}' title='{@label}' id='{@class}_{@name}'>
        <xsl:apply-templates select='//var[@class=current()/@name]' />
    </fieldset>
</xsl:template>

<xsl:template match='field[@class="result"]'>
    <div class='{@class}' title='{@label}' id='{@class}_{@name}'>
        <table>
            <xsl:apply-templates select='//profile/point' />
        </table>
    </div>
</xsl:template>

<xsl:template match='field[@class="img"]'>
    <div class='{@class}' title='{@label}' id='{@class}_{@name}'>
        <img src='{@src}' alt='{@label}' />
    </div>
</xsl:template>

<xsl:template match='var'>
    <label for='{@class}_{@name}'>
        <xsl:value-of select='@label' />
        <xsl:if test='@short'>
            <xsl:text> </xsl:text>
            <span class='short' id='short_{@name}'>
                <xsl:value-of select='@short' />
            </span>
        </xsl:if>
        <xsl:text>, </xsl:text>
        <span class='units' id='units_{@name}'>
            <xsl:value-of select='@unit' />
        </span>
    </label>
    <input type='text' name='{@name}' value='{.}' title='{@label}' id='{@class}_{@name}'>
    <xsl:if test='./@checkinput="error"'>
        <xsl:attribute name='class'>error</xsl:attribute>
    </xsl:if>
    </input>
</xsl:template>

<xsl:template match='point'>
    <tr>
        <td>
            <xsl:value-of select='.' />
            <br />
        </td>
    </tr>
</xsl:template>

<xsl:template match='profile' />

<xsl:template match='vars' />

</xsl:stylesheet>
