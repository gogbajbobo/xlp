<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version="1.0">

<xsl:output method="xml"
            indent="yes"
            encoding="utf-8"
            omit-xml-declaration="no" />

<xsl:template match='@* | node()'>
    <xsl:copy>
        <xsl:apply-templates select='@* | node()' />
    </xsl:copy>
</xsl:template>

<xsl:template match='var'>
    <xsl:variable name='userinput-value' select='//userinput/@*[name()=current()/@name]' />
    <xsl:variable name='checkinput-value' select='//checkinput/@*[name()=current()/@name]' />
    <xsl:copy>
        <xsl:apply-templates select='@*' />
        <xsl:if test='$checkinput-value'>
            <xsl:attribute name='checkinput'>
                <xsl:value-of select='$checkinput-value' />
            </xsl:attribute>
        </xsl:if>
        <xsl:if test='$userinput-value'>
            <xsl:value-of select='$userinput-value' />
        </xsl:if>
        <xsl:if test='not($userinput-value)'>
            <xsl:value-of select='.' />
        </xsl:if>
    </xsl:copy>
</xsl:template>

<xsl:template match='*[@disable="true"]' />    

<xsl:template match='field[@class="result"]'>
    <xsl:if test='not(//checkinput[@*="error"])'>
        <xsl:copy-of select='.' />
    </xsl:if>
</xsl:template>

</xsl:stylesheet>
