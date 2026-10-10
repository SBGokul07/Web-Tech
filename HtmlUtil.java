package com.kpr.portal.util;

/** Escapes text before printing it in a JSP, so that HTML / script inside data is not executed. */
public class HtmlUtil {

    public static String esc(String text) {
        if (text == null) {
            return "";
        }
        return text.replace("&", "&amp;")
                   .replace("<", "&lt;")
                   .replace(">", "&gt;")
                   .replace("\"", "&quot;")
                   .replace("'", "&#39;");
    }
}
