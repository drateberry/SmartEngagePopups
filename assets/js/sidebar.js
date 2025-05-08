/**
 * SmartEngage Popups Gutenberg Sidebar
 *
 * @package SmartEngage_Popups
 */

const { registerPlugin } = wp.plugins;
const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
const { __ } = wp.i18n;
const { compose } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { 
    PanelBody, 
    TextControl, 
    SelectControl, 
    ToggleControl, 
    Button, 
    RangeControl 
} = wp.components;

/**
 * SmartEngage Popups Sidebar Component
 */
const SmartEngageSidebar = compose(
    withSelect( ( select ) => {
        const { getEditedPostAttribute } = select( 'core/editor' );
        const { getPostType } = select( 'core' );
        
        // Only show sidebar for our custom post type
        const postType = getPostType( getEditedPostAttribute( 'type' ) );
        const showSidebar = postType && postType.slug === 'smartengage_popup';
        
        return {
            showSidebar,
            popupType: select( 'core/editor' ).getEditedPostMeta()._smartengage_popup_type || 'slide-in',
            popupPosition: select( 'core/editor' ).getEditedPostMeta()._smartengage_popup_position || 'bottom-right',
            popupStatus: select( 'core/editor' ).getEditedPostMeta()._smartengage_popup_status || 'enabled',
            ctaText: select( 'core/editor' ).getEditedPostMeta()._smartengage_cta_text || '',
            ctaUrl: select( 'core/editor' ).getEditedPostMeta()._smartengage_cta_url || '',
            timeOnPage: select( 'core/editor' ).getEditedPostMeta()._smartengage_time_on_page || 10,
            scrollDepth: select( 'core/editor' ).getEditedPostMeta()._smartengage_scroll_depth || 50,
            exitIntent: select( 'core/editor' ).getEditedPostMeta()._smartengage_exit_intent || 'disabled',
        };
    } ),
    withDispatch( ( dispatch ) => {
        return {
            updateMeta: ( key, value ) => {
                dispatch( 'core/editor' ).editPost( { meta: { [key]: value } } );
            }
        };
    } )
)( ( props ) => {
    // Don't render if not our post type
    if ( !props.showSidebar ) {
        return null;
    }
    
    return (
        <>
            <PluginSidebarMoreMenuItem
                target="smartengage-sidebar"
                icon="megaphone"
            >
                { __( 'Popup Settings', 'smartengage-popups' ) }
            </PluginSidebarMoreMenuItem>
            <PluginSidebar
                name="smartengage-sidebar"
                title={ __( 'Popup Settings', 'smartengage-popups' ) }
                icon="megaphone"
            >
                <div className="smartengage-sidebar-panel">
                    <PanelBody
                        title={ __( 'Basic Options', 'smartengage-popups' ) }
                        initialOpen={ true }
                    >
                        <SelectControl
                            label={ __( 'Popup Status', 'smartengage-popups' ) }
                            value={ props.popupStatus }
                            options={ [
                                { label: __( 'Enabled', 'smartengage-popups' ), value: 'enabled' },
                                { label: __( 'Disabled', 'smartengage-popups' ), value: 'disabled' },
                            ] }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_popup_status', value ) }
                        />
                        
                        <SelectControl
                            label={ __( 'Popup Type', 'smartengage-popups' ) }
                            value={ props.popupType }
                            options={ [
                                { label: __( 'Slide-in', 'smartengage-popups' ), value: 'slide-in' },
                                { label: __( 'Full-screen', 'smartengage-popups' ), value: 'full-screen' },
                            ] }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_popup_type', value ) }
                        />
                        
                        { props.popupType === 'slide-in' && (
                            <SelectControl
                                label={ __( 'Popup Position', 'smartengage-popups' ) }
                                value={ props.popupPosition }
                                options={ [
                                    { label: __( 'Bottom Right', 'smartengage-popups' ), value: 'bottom-right' },
                                    { label: __( 'Bottom Left', 'smartengage-popups' ), value: 'bottom-left' },
                                    { label: __( 'Center', 'smartengage-popups' ), value: 'center' },
                                ] }
                                onChange={ ( value ) => props.updateMeta( '_smartengage_popup_position', value ) }
                            />
                        ) }
                        
                        <TextControl
                            label={ __( 'CTA Button Text', 'smartengage-popups' ) }
                            value={ props.ctaText }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_cta_text', value ) }
                        />
                        
                        <TextControl
                            label={ __( 'CTA Button URL', 'smartengage-popups' ) }
                            value={ props.ctaUrl }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_cta_url', value ) }
                        />
                    </PanelBody>
                    
                    <PanelBody
                        title={ __( 'Trigger Settings', 'smartengage-popups' ) }
                        initialOpen={ false }
                    >
                        <RangeControl
                            label={ __( 'Time on Page (seconds)', 'smartengage-popups' ) }
                            value={ props.timeOnPage }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_time_on_page', value ) }
                            min={ 1 }
                            max={ 120 }
                        />
                        
                        <RangeControl
                            label={ __( 'Scroll Depth (%)', 'smartengage-popups' ) }
                            value={ props.scrollDepth }
                            onChange={ ( value ) => props.updateMeta( '_smartengage_scroll_depth', value ) }
                            min={ 0 }
                            max={ 100 }
                        />
                        
                        <ToggleControl
                            label={ __( 'Enable Exit Intent', 'smartengage-popups' ) }
                            checked={ props.exitIntent === 'enabled' }
                            onChange={ ( checked ) => props.updateMeta( '_smartengage_exit_intent', checked ? 'enabled' : 'disabled' ) }
                        />
                    </PanelBody>
                    
                    <PanelBody
                        title={ __( 'Preview', 'smartengage-popups' ) }
                        initialOpen={ false }
                    >
                        <p>{ __( 'Save the popup to preview changes.', 'smartengage-popups' ) }</p>
                        <Button
                            isPrimary
                            onClick={ () => {
                                alert( __( 'Please save the popup to see a preview.', 'smartengage-popups' ) );
                            } }
                        >
                            { __( 'Preview Popup', 'smartengage-popups' ) }
                        </Button>
                    </PanelBody>
                </div>
            </PluginSidebar>
        </>
    );
} );

// Register Gutenberg sidebar plugin
registerPlugin( 'smartengage-sidebar', {
    render: SmartEngageSidebar,
} );
