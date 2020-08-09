<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 *
 */

namespace Koded\Stdlib;

/**
 * Helper class Mime.
 *
 */
final class Mime
{

    private static $extensionsToTypes = [
        '323'        => ['text/h323'],
        '7z'         => ['application/x-7z-compressed'],
        'abw'        => ['application/x-abiword'],
        'acx'        => ['application/internet-property-stream'],
        'ai'         => ['application/postscript'],
        'aif'        => ['audio/x-aiff'],
        'aifc'       => ['audio/x-aiff'],
        'aiff'       => ['audio/x-aiff'],
        'asf'        => ['video/x-ms-asf'],
        'asr'        => ['video/x-ms-asf'],
        'asx'        => ['video/x-ms-asf'],
        'atom'       => ['application/atom+xml'],
        'avi'        => ['video/avi', 'video/msvideo', 'video/x-msvideo'],
        'bin'        => ['application/octet-stream', 'application/macbinary'],
        'bmp'        => ['image/bmp'],
        'c'          => ['text/x-csrc'],
        'c++'        => ['text/x-c++src'],
        'cab'        => ['application/x-cab'],
        'cc'         => ['text/x-c++src'],
        'cda'        => ['application/x-cdf'],
        'class'      => ['application/octet-stream'],
        'cpp'        => ['text/x-c++src'],
        'cpt'        => ['application/mac-compactpro'],
        'csh'        => ['text/x-csh'],
        'css'        => ['text/css'],
        'csv'        => [
            'text/x-comma-separated-values',
            'application/vnd.ms-excel',
            'text/comma-separated-values',
            'text/csv'
        ],
        'dbk'        => ['application/docbook+xml'],
        'dcr'        => ['application/x-director'],
        'deb'        => ['application/x-debian-package'],
        'diff'       => ['text/x-diff'],
        'dir'        => ['application/x-director'],
        'divx'       => ['video/divx'],
        'dll'        => ['application/octet-stream', 'application/x-msdos-program'],
        'dmg'        => ['application/x-apple-diskimage'],
        'dms'        => ['application/octet-stream'],
        'doc'        => ['application/msword'],
        'dvi'        => ['application/x-dvi'],
        'dxr'        => ['application/x-director'],
        'eml'        => ['message/rfc822'],
        'eps'        => ['application/postscript'],
        'evy'        => ['application/envoy'],
        'exe'        => ['application/x-msdos-program', 'application/octet-stream'],
        'fla'        => ['application/octet-stream'],
        'flac'       => ['application/x-flac'],
        'flc'        => ['video/flc'],
        'fli'        => ['video/fli'],
        'flv'        => ['video/x-flv'],
        'gif'        => ['image/gif'],
        'gtar'       => ['application/x-gtar'],
        'gz'         => ['application/x-gzip'],
        'h'          => ['text/x-chdr'],
        'h++'        => ['text/x-c++hdr'],
        'hh'         => ['text/x-c++hdr'],
        'hpp'        => ['text/x-c++hdr'],
        'hqx'        => ['application/mac-binhex40'],
        'hs'         => ['text/x-haskell'],
        'htm'        => ['text/html'],
        'html'       => ['text/html'],
        'ico'        => ['image/x-icon'],
        'ics'        => ['text/calendar'],
        'iii'        => ['application/x-iphone'],
        'ins'        => ['application/x-internet-signup'],
        'iso'        => ['application/x-iso9660-image'],
        'isp'        => ['application/x-internet-signup'],
        'jar'        => ['application/java-archive'],
        'java'       => ['application/x-java-applet'],
        'javascript' => ['application/javascript'],
        'jpe'        => ['image/jpeg', 'image/pjpeg'],
        'jpeg'       => ['image/jpeg', 'image/pjpeg'],
        'jpg'        => ['image/jpeg', 'image/pjpeg'],
        'js'         => ['application/x-javascript'],
        'json'       => ['application/json'],
        'latex'      => ['application/x-latex'],
        'lha'        => ['application/octet-stream'],
        'log'        => ['text/plain', 'text/x-log'],
        'lzh'        => ['application/octet-stream'],
        'm4a'        => ['audio/mpeg'],
        'm4p'        => ['video/mp4v-es'],
        'm4v'        => ['video/mp4'],
        'man'        => ['application/x-troff-man'],
        'mdb'        => ['application/x-msaccess'],
        'midi'       => ['audio/midi'],
        'mid'        => ['audio/midi'],
        'mif'        => ['application/vnd.mif'],
        'mka'        => ['audio/x-matroska'],
        'mkv'        => ['video/x-matroska'],
        'mov'        => ['video/quicktime'],
        'movie'      => ['video/x-sgi-movie'],
        'mp2'        => ['audio/mpeg'],
        'mp3'        => ['audio/mpeg'],
        'mp4'        => ['application/mp4', 'audio/mp4', 'video/mp4'],
        'mpa'        => ['video/mpeg'],
        'mpe'        => ['video/mpeg'],
        'mpeg'       => ['video/mpeg'],
        'mpg'        => ['video/mpeg'],
        'mpg4'       => ['video/mp4'],
        'mpga'       => ['audio/mpeg'],
        'mpp'        => ['application/vnd.ms-project'],
        'mpv'        => ['video/x-matroska'],
        'mpv2'       => ['video/mpeg'],
        'ms'         => ['application/x-troff-ms'],
        'msg'        => ['application/msoutlook', 'application/x-msg'],
        'msi'        => ['application/x-msi'],
        'nws'        => ['message/rfc822'],
        'oda'        => ['application/oda'],
        'odb'        => ['application/vnd.oasis.opendocument.database'],
        'odc'        => ['application/vnd.oasis.opendocument.chart'],
        'odf'        => ['application/vnd.oasis.opendocument.forumla'],
        'odg'        => ['application/vnd.oasis.opendocument.graphics'],
        'odi'        => ['application/vnd.oasis.opendocument.image'],
        'odm'        => ['application/vnd.oasis.opendocument.text-master'],
        'odp'        => ['application/vnd.oasis.opendocument.presentation'],
        'ods'        => ['application/vnd.oasis.opendocument.spreadsheet'],
        'odt'        => ['application/vnd.oasis.opendocument.text'],
        'oga'        => ['audio/ogg'],
        'ogg'        => ['application/ogg'],
        'ogv'        => ['video/ogg'],
        'otg'        => ['application/vnd.oasis.opendocument.graphics-template'],
        'oth'        => ['application/vnd.oasis.opendocument.web'],
        'otp'        => ['application/vnd.oasis.opendocument.presentation-template'],
        'ots'        => ['application/vnd.oasis.opendocument.spreadsheet-template'],
        'ott'        => ['application/vnd.oasis.opendocument.template'],
        'p'          => ['text/x-pascal'],
        'pas'        => ['text/x-pascal'],
        'patch'      => ['text/x-diff'],
        'pbm'        => ['image/x-portable-bitmap'],
        'pdf'        => ['application/pdf', 'application/x-download'],
        'php'        => ['application/x-httpd-php'],
        'php3'       => ['application/x-httpd-php'],
        'php4'       => ['application/x-httpd-php'],
        'php5'       => ['application/x-httpd-php'],
        'phps'       => ['application/x-httpd-php-source'],
        'phtml'      => ['application/x-httpd-php'],
        'pl'         => ['text/x-perl'],
        'pm'         => ['text/x-perl'],
        'png'        => ['image/png', 'image/x-png'],
        'po'         => ['text/x-gettext-translation'],
        'pot'        => ['application/vnd.ms-powerpoint'],
        'pps'        => ['application/vnd.ms-powerpoint'],
        'ppt'        => ['application/powerpoint'],
        'ps'         => ['application/postscript'],
        'psd'        => ['application/x-photoshop', 'image/x-photoshop'],
        'pub'        => ['application/x-mspublisher'],
        'py'         => ['text/x-python'],
        'qt'         => ['video/quicktime'],
        'ra'         => ['audio/x-realaudio'],
        'ram'        => ['audio/x-realaudio', 'audio/x-pn-realaudio'],
        'rar'        => ['application/rar'],
        'rgb'        => ['image/x-rgb'],
        'rm'         => ['audio/x-pn-realaudio'],
        'rpm'        => ['audio/x-pn-realaudio-plugin', 'application/x-redhat-package-manager'],
        'rss'        => ['application/rss+xml'],
        'rtf'        => ['text/rtf'],
        'rtx'        => ['text/richtext'],
        'rv'         => ['video/vnd.rn-realvideo'],
        'sea'        => ['application/octet-stream'],
        'sh'         => ['text/x-sh'],
        'shtml'      => ['text/html'],
        'sit'        => ['application/x-stuffit'],
        'smi'        => ['application/smil'],
        'smil'       => ['application/smil'],
        'so'         => ['application/octet-stream'],
        'src'        => ['application/x-wais-source'],
        'svg'        => ['image/svg+xml'],
        'swf'        => ['application/x-shockwave-flash'],
        't'          => ['application/x-troff'],
        'tar'        => ['application/x-tar'],
        'tcl'        => ['text/x-tcl'],
        'tex'        => ['application/x-tex'],
        'text'       => ['text/plain'],
        'texti'      => ['application/x-texinfo'],
        'textinfo'   => ['application/x-texinfo'],
        'tgz'        => ['application/x-tar'],
        'tif'        => ['image/tiff'],
        'tiff'       => ['image/tiff'],
        'torrent'    => ['application/x-bittorrent'],
        'tr'         => ['application/x-troff'],
        'tsv'        => ['text/tab-separated-values'],
        'txt'        => ['text/plain'],
        'wav'        => ['audio/x-wav'],
        'wax'        => ['audio/x-ms-wax'],
        'wbxml'      => ['application/wbxml'],
        'wm'         => ['video/x-ms-wm'],
        'wma'        => ['audio/x-ms-wma'],
        'wmd'        => ['application/x-ms-wmd'],
        'wmlc'       => ['application/wmlc'],
        'wmv'        => ['video/x-ms-wmv', 'application/octet-stream'],
        'wmx'        => ['video/x-ms-wmx'],
        'wmz'        => ['application/x-ms-wmz'],
        'word'       => ['application/msword', 'application/octet-stream'],
        'wp5'        => ['application/wordperfect5.1'],
        'wpd'        => ['application/vnd.wordperfect'],
        'wvx'        => ['video/x-ms-wvx'],
        'xbm'        => ['image/x-xbitmap'],
        'xcf'        => ['image/xcf'],
        'xhtml'      => ['application/xhtml+xml'],
        'xht'        => ['application/xhtml+xml'],
        'xl'         => ['application/excel', 'application/vnd.ms-excel'],
        'xla'        => ['application/excel', 'application/vnd.ms-excel'],
        'xlc'        => ['application/excel', 'application/vnd.ms-excel'],
        'xlm'        => ['application/excel', 'application/vnd.ms-excel'],
        'xls'        => ['application/excel', 'application/vnd.ms-excel'],
        'xlt'        => ['application/excel', 'application/vnd.ms-excel'],
        'xml'        => ['text/xml', 'application/xml'],
        'xof'        => ['x-world/x-vrml'],
        'xpm'        => ['image/x-xpixmap'],
        'xsl'        => ['text/xml'],
        'xvid'       => ['video/x-xvid'],
        'xwd'        => ['image/x-xwindowdump'],
        'z'          => ['application/x-compress'],
        'zip'        => ['application/x-zip', 'application/zip', 'application/x-zip-compressed'],
    ];

    /**
     * @var array
     */
    private static $typesToExtensions = [
        'application/json'                                         => ['json'],
        'text/xml'                                                 => ['xml', 'xsl'],
        'application/xml'                                          => ['xml'],
        'text/html'                                                => ['htm', 'html', 'shtml'],
        'text/plain'                                               => ['log', 'text', 'txt'],
        'application/xhtml+xml'                                    => ['xhtml', 'xht'],
        'image/png'                                                => ['png'],
        'image/x-png'                                              => ['png'],
        'image/gif'                                                => ['gif'],
        'application/javascript'                                   => ['javascript'],
        'image/jpeg'                                               => ['jpe', 'jpeg', 'jpg'],
        'image/pjpeg'                                              => ['jpe', 'jpeg', 'jpg'],
        'application/x-javascript'                                 => ['js'],
        'image/svg+xml'                                            => ['svg'],
        'application/x-latex'                                      => ['latex'],
        'text/x-log'                                               => ['log'],
        'application/x-7z-compressed'                              => ['7z'],
        'application/x-abiword'                                    => ['abw'],
        'application/internet-property-stream'                     => ['acx'],
        'application/postscript'                                   => ['ai', 'eps', 'ps'],
        'audio/x-aiff'                                             => ['aif', 'aifc', 'aiff'],
        'video/x-ms-asf'                                           => ['asf', 'asr', 'asx'],
        'application/atom+xml'                                     => ['atom'],
        'video/avi'                                                => ['avi'],
        'video/msvideo'                                            => ['avi'],
        'video/x-msvideo'                                          => ['avi'],
        'application/octet-stream'                                 => [
            'bin',
            'class',
            'dll',
            'dms',
            'exe',
            'fla',
            'lha',
            'lzh',
            'sea',
            'so',
            'wmv',
            'word'
        ],
        'application/macbinary'                                    => ['bin'],
        'image/bmp'                                                => ['bmp'],
        'text/x-csrc'                                              => ['c'],
        'text/x-c++src'                                            => ['c++', 'cc', 'cpp'],
        'application/x-cab'                                        => ['cab'],
        'application/x-cdf'                                        => ['cda'],
        'application/mac-compactpro'                               => ['cpt'],
        'text/x-csh'                                               => ['csh'],
        'text/css'                                                 => ['css'],
        'text/x-comma-separated-values'                            => ['csv'],
        'application/vnd.ms-excel'                                 => ['csv', 'xl', 'xla', 'xlc', 'xlm', 'xls', 'xlt'],
        'text/comma-separated-values'                              => ['csv'],
        'text/csv'                                                 => ['csv'],
        'application/docbook+xml'                                  => ['dbk'],
        'application/x-director'                                   => ['dcr', 'dir', 'dxr'],
        'application/x-debian-package'                             => ['deb'],
        'text/x-diff'                                              => ['diff', 'patch'],
        'video/divx'                                               => ['divx'],
        'application/x-msdos-program'                              => ['dll', 'exe'],
        'application/x-apple-diskimage'                            => ['dmg'],
        'application/msword'                                       => ['doc', 'word'],
        'application/x-dvi'                                        => ['dvi'],
        'message/rfc822'                                           => ['eml', 'nws'],
        'application/envoy'                                        => ['evy'],
        'application/x-flac'                                       => ['flac'],
        'video/flc'                                                => ['flc'],
        'video/fli'                                                => ['fli'],
        'video/x-flv'                                              => ['flv'],
        'application/x-gtar'                                       => ['gtar'],
        'application/x-gzip'                                       => ['gz'],
        'text/x-chdr'                                              => ['h'],
        'text/x-c++hdr'                                            => ['h++', 'hh', 'hpp'],
        'application/mac-binhex40'                                 => ['hqx'],
        'text/x-haskell'                                           => ['hs'],
        'image/x-icon'                                             => ['ico'],
        'text/calendar'                                            => ['ics'],
        'application/x-iphone'                                     => ['iii'],
        'application/x-internet-signup'                            => ['ins', 'isp'],
        'application/x-iso9660-image'                              => ['iso'],
        'application/java-archive'                                 => ['jar'],
        'application/x-java-applet'                                => ['java'],
        'audio/mpeg'                                               => ['m4a', 'mp2', 'mp3', 'mpga'],
        'video/mp4v-es'                                            => ['m4p'],
        'video/mp4'                                                => ['m4v', 'mp4', 'mpg4'],
        'application/x-troff-man'                                  => ['man'],
        'application/x-msaccess'                                   => ['mdb'],
        'audio/midi'                                               => ['midi', 'mid'],
        'application/vnd.mif'                                      => ['mif'],
        'audio/x-matroska'                                         => ['mka'],
        'video/x-matroska'                                         => ['mkv', 'mpv'],
        'video/quicktime'                                          => ['mov', 'qt'],
        'video/x-sgi-movie'                                        => ['movie'],
        'application/mp4'                                          => ['mp4'],
        'audio/mp4'                                                => ['mp4'],
        'video/mpeg'                                               => ['mpa', 'mpe', 'mpeg', 'mpg', 'mpv2'],
        'application/vnd.ms-project'                               => ['mpp'],
        'application/x-troff-ms'                                   => ['ms'],
        'application/msoutlook'                                    => ['msg'],
        'application/x-msg'                                        => ['msg'],
        'application/x-msi'                                        => ['msi'],
        'application/oda'                                          => ['oda'],
        'application/vnd.oasis.opendocument.database'              => ['odb'],
        'application/vnd.oasis.opendocument.chart'                 => ['odc'],
        'application/vnd.oasis.opendocument.forumla'               => ['odf'],
        'application/vnd.oasis.opendocument.graphics'              => ['odg'],
        'application/vnd.oasis.opendocument.image'                 => ['odi'],
        'application/vnd.oasis.opendocument.text-master'           => ['odm'],
        'application/vnd.oasis.opendocument.presentation'          => ['odp'],
        'application/vnd.oasis.opendocument.spreadsheet'           => ['ods'],
        'application/vnd.oasis.opendocument.text'                  => ['odt'],
        'audio/ogg'                                                => ['oga'],
        'application/ogg'                                          => ['ogg'],
        'video/ogg'                                                => ['ogv'],
        'application/vnd.oasis.opendocument.graphics-template'     => ['otg'],
        'application/vnd.oasis.opendocument.web'                   => ['oth'],
        'application/vnd.oasis.opendocument.presentation-template' => ['otp'],
        'application/vnd.oasis.opendocument.spreadsheet-template'  => ['ots'],
        'application/vnd.oasis.opendocument.template'              => ['ott'],
        'text/x-pascal'                                            => ['p', 'pas'],
        'image/x-portable-bitmap'                                  => ['pbm'],
        'application/pdf'                                          => ['pdf'],
        'application/x-download'                                   => ['pdf'],
        'application/x-httpd-php'                                  => ['php', 'php3', 'php4', 'php5', 'phtml'],
        'application/x-httpd-php-source'                           => ['phps'],
        'text/x-perl'                                              => ['pl', 'pm'],
        'text/x-gettext-translation'                               => ['po'],
        'application/vnd.ms-powerpoint'                            => ['pot', 'pps'],
        'application/powerpoint'                                   => ['ppt'],
        'application/x-photoshop'                                  => ['psd'],
        'image/x-photoshop'                                        => ['psd'],
        'application/x-mspublisher'                                => ['pub'],
        'text/x-python'                                            => ['py'],
        'audio/x-realaudio'                                        => ['ra', 'ram'],
        'audio/x-pn-realaudio'                                     => ['ram', 'rm'],
        'application/rar'                                          => ['rar'],
        'image/x-rgb'                                              => ['rgb'],
        'audio/x-pn-realaudio-plugin'                              => ['rpm'],
        'application/x-redhat-package-manager'                     => ['rpm'],
        'application/rss+xml'                                      => ['rss'],
        'text/rtf'                                                 => ['rtf'],
        'text/richtext'                                            => ['rtx'],
        'video/vnd.rn-realvideo'                                   => ['rv'],
        'text/x-sh'                                                => ['sh'],
        'application/x-stuffit'                                    => ['sit'],
        'application/smil'                                         => ['smi', 'smil'],
        'application/x-wais-source'                                => ['src'],
        'application/x-shockwave-flash'                            => ['swf'],
        'application/x-troff'                                      => ['t', 'tr'],
        'application/x-tar'                                        => ['tar', 'tgz'],
        'text/x-tcl'                                               => ['tcl'],
        'application/x-tex'                                        => ['tex'],
        'application/x-texinfo'                                    => ['texti', 'textinfo'],
        'image/tiff'                                               => ['tif', 'tiff'],
        'application/x-bittorrent'                                 => ['torrent'],
        'text/tab-separated-values'                                => ['tsv'],
        'audio/x-wav'                                              => ['wav'],
        'audio/x-ms-wax'                                           => ['wax'],
        'application/wbxml'                                        => ['wbxml'],
        'video/x-ms-wm'                                            => ['wm'],
        'audio/x-ms-wma'                                           => ['wma'],
        'application/x-ms-wmd'                                     => ['wmd'],
        'application/wmlc'                                         => ['wmlc'],
        'video/x-ms-wmv'                                           => ['wmv'],
        'video/x-ms-wmx'                                           => ['wmx'],
        'application/x-ms-wmz'                                     => ['wmz'],
        'application/wordperfect5.1'                               => ['wp5'],
        'application/vnd.wordperfect'                              => ['wpd'],
        'video/x-ms-wvx'                                           => ['wvx'],
        'image/x-xbitmap'                                          => ['xbm'],
        'image/xcf'                                                => ['xcf'],
        'application/excel'                                        => ['xl', 'xla', 'xlc', 'xlm', 'xls', 'xlt'],
        'x-world/x-vrml'                                           => ['xof'],
        'image/x-xpixmap'                                          => ['xpm'],
        'video/x-xvid'                                             => ['xvid'],
        'image/x-xwindowdump'                                      => ['xwd'],
        'application/x-compress'                                   => ['z'],
        'application/x-zip'                                        => ['zip'],
        'application/zip'                                          => ['zip'],
        'application/x-zip-compressed'                             => ['zip'],
        'text/h323'                                                => ['323'],
    ];

    /**
     * Returns the mime type by file extension name.
     *
     * @param string $ext   The short mime type name
     * @param int    $index [optional] The index of the full-name mime from the array list
     *
     * @return string The media type, or
     * empty string if type was not found (by type and index)
     */
    public static function type(string $ext, int $index = 0): string
    {
        return self::$extensionsToTypes[$ext][$index] ?? self::$extensionsToTypes[$ext][0] ?? '';
    }

    /**
     * Returns the list of mime types associated by the file extension name.
     *
     * @param string $ext Extension name
     *
     * @return array The mime types list
     */
    public static function types(string $ext): array
    {
        return self::$extensionsToTypes[$ext] ?? [];
    }

    /**
     * Checks if mime type is in the supported type list.
     *
     * @param string $type
     *
     * @return bool
     */
    public static function supports(string $type): bool
    {
        return array_key_exists($type, self::$typesToExtensions);
    }

    /**
     * Returns the list of file extensions associated by the mime type.
     *
     * @param string $type Mime type
     *
     * @return array List of corresponding file extensions
     */
    public static function extensions(string $type): array
    {
        return self::$typesToExtensions[$type] ?? [];
    }
}
