<?php
/*
 * <style type="text/css">
	.embeddedServiceHelpButton .helpButton .uiButton, div .chatHeaderBranding, div .embeddedServiceSidebarButton {
		background-color: var(--wp--preset--color--primary);
		font-family: "district-pro","Arial", sans-serif;
font-weight: 600;
  font-size: 1rem;
	}
	.embeddedServiceHelpButton .helpButton .uiButton:focus {
		outline: 1px solid var(--wp--preset--color--primary);
	}
</style>

<script type="text/javascript" src="https://service.force.com/embeddedservice/5.0/esw.min.js"></script>
<script type="text/javascript">
	var initESW = function(gslbBaseURL) {
		embedded_svc.settings.displayHelpButton = true; //Or false
		embedded_svc.settings.language = ''; //For example, enter 'en' or 'en-US'

		embedded_svc.settings.defaultMinimizedText = 'Chat Live'; //(Defaults to Chat with an Expert)
		embedded_svc.settings.disabledMinimizedText = '<a style="color: #ffffff;" href="https://www.irafinancialgroup.com/contact-us/">Contact Us</a>' //(Defaults to Agent Offline)

		//embedded_svc.settings.loadingText = ''; //(Defaults to Loading)
		//embedded_svc.settings.storageDomain = 'yourdomain.com'; //(Sets the domain for your deployment so that visitors can navigate subdomains during a chat session)

		// Settings for Chat
		//embedded_svc.settings.directToButtonRouting = function(prechatFormData) {
			// Dynamically changes the button ID based on what the visitor enters in the pre-chat form.
			// Returns a valid button ID.
		//};
		//embedded_svc.settings.prepopulatedPrechatFields = {}; //Sets the auto-population of pre-chat form fields
		//embedded_svc.settings.fallbackRouting = []; //An array of button IDs, user IDs, or userId_buttonId
		//embedded_svc.settings.offlineSupportMinimizedText = '...'; //(Defaults to Contact Us)
		embedded_svc.settings.extraPrechatFormDetails = [
            {"label":"How Can We Help You?", "transcriptFields": ["Chat_Topic__c"]},
            {"label":"Are You A Current Customer?", "transcriptFields": ["Are_you_a_current_customer__c"]}
        ];

		embedded_svc.settings.enabledFeatures = ['LiveAgent'];
		embedded_svc.settings.entryFeature = 'LiveAgent';

		embedded_svc.init(
			'https://irafinancial1.my.salesforce.com',
			'https://irafinancial1.my.salesforce-sites.com/',
			gslbBaseURL,
			'00D5f000005UUMf',
			'Chat_Users',
			{
				baseLiveAgentContentURL: 'https://c.la13-core2.sfdc-lywfpd.salesforceliveagent.com/content',
				deploymentId: '5725f000000YyU2',
				buttonId: '5735f000000YzDf',
				baseLiveAgentURL: 'https://d.la13-core2.sfdc-lywfpd.salesforceliveagent.com/chat',
				eswLiveAgentDevName: 'EmbeddedServiceLiveAgent_Parent04I5f000000TmW9EAK_187fdbf7a1c',
				isOfflineSupportEnabled: false
			}
		);
	};

	if (!window.embedded_svc) {
		var s = document.createElement('script');
		s.setAttribute('src', 'https://irafinancial1.my.salesforce.com/embeddedservice/5.0/esw.min.js');
		s.onload = function() {
			initESW(null);
		};
		document.body.appendChild(s);
	} else {
		initESW('https://service.force.com');
	}
</script>*/

if (!defined('ABSPATH')) exit;

/**
 * Shortcode for Salesforce embedded chat
 *
 * Usage: [sf_chat button_text="Chat Now" offline_text="Contact Us" offline_url="/contact-us/" theme_color=""]
 */
function lvl_salesforce_chat_shortcode($atts)
{
	// Extract and set default attributes
	$atts = shortcode_atts(array(
		'button_text'  => 'Chat Live',
		'offline_text' => 'Contact Us',
		'offline_url'  => '/contact-us/',
		'offline_hide' => "true",
		'theme_color'  => 'var(--wp--preset--color--primary)',
		'font_family'  => '"district-pro","Arial", sans-serif',
		'font_weight'  => '600',
		'font_size'    => '1rem',
		'sf_org_url'   => 'https://irafinancial1.my.salesforce.com',
		'sf_site_url'  => 'https://irafinancial1.my.salesforce-sites.com/',
		'sf_org_id'    => '00D5f000005UUMf',
		'sf_chat_id'   => '5735f000000YzDf',
		'sf_deploy_id' => '5725f000000YyU2',
		'sf_agent_dev' => 'EmbeddedServiceLiveAgent_Parent04I5f000000TmW9EAK_187fdbf7a1c',
	), $atts);

	// Create a unique ID for this instance of the chat button
	$chat_id = 'sf-chat-' . wp_rand(10000, 99999);

	// Build the CSS for the chat button
	$css = "
    <style type='text/css'>
        .embeddedServiceHelpButton .helpButton .uiButton,
        div .chatHeaderBranding,
        div .embeddedServiceSidebarButton {
            background-color: {$atts['theme_color']};
            font-family: {$atts['font_family']};
            font-weight: {$atts['font_weight']};
            font-size: {$atts['font_size']};
        }
        .embeddedServiceHelpButton .helpButton .uiButton:focus {
            outline: 1px solid {$atts['theme_color']};
        }
        " . ($atts['offline_hide'] == "true" ? ".embeddedServiceHelpButton:has(.message > a) { display: none; }" : "") . "
    </style>";

	// Create custom chat link (instead of using Salesforce's default button)
	$button_html = ''; //"<a href='#start-chat' class='sf-chat-button' id='{$chat_id}'>{$atts['button_text']}</a>";

	// Salesforce initialization script
	$script = "
	<script type='text/javascript'>
		if (typeof window.initSalesforceChat === 'undefined') {
			window.initSalesforceChat = function() {
				if (!document.getElementById('sfdc-chat-script')) {
					var s = document.createElement('script');
					s.id = 'sfdc-chat-script';
					s.setAttribute('src', '{$atts['sf_org_url']}/embeddedservice/5.0/esw.min.js');
					s.onload = function() {
						initESW(null);
					};
					document.body.appendChild(s);
				} else {
					if (typeof initESW === 'function') {
						initESW('https://service.force.com');
					}
				}
			};

			var initESW = function(gslbBaseURL) {
				embedded_svc.settings.displayHelpButton = true;
				embedded_svc.settings.language = '';

				embedded_svc.settings.defaultMinimizedText = '{$atts['button_text']}';
				embedded_svc.settings.disabledMinimizedText = '<a style=\"color: #ffffff;\" href=\"{$atts['offline_url']}\">{$atts['offline_text']}</a>';

				// Listen for agent availability changes
				embedded_svc.addEventHandler('onAgentAvailabilityChange', function(data) {
					console.log('Agent availability changed:', data);
					var helpButton = document.querySelector('.embeddedServiceHelpButton');
					if (helpButton) {
						console.log('Help button found:', data.isAgentAvailable);
						if (data.isAgentAvailable) {
							// Make button visible when agents are online
							helpButton.style.display = 'block';
						} else {
							// Hide button when agents are offline
							helpButton.style.display = 'none';
						}
					}
				});

				// Hide the button initially until we know agent status
				embedded_svc.settings.widgetWidth = '320px';
				embedded_svc.settings.widgetHeight = '498px';

				embedded_svc.settings.extraPrechatFormDetails = [
					{\"label\":\"How Can We Help You?\", \"transcriptFields\": [\"Chat_Topic__c\"]},
					{\"label\":\"Are You A Current Customer?\", \"transcriptFields\": [\"Are_you_a_current_customer__c\"]}
				];

				embedded_svc.settings.enabledFeatures = ['LiveAgent'];
				embedded_svc.settings.entryFeature = 'LiveAgent';

				embedded_svc.init(
					'{$atts['sf_org_url']}',
					'{$atts['sf_site_url']}',
					gslbBaseURL,
					'{$atts['sf_org_id']}',
					'Chat_Users',
					{
						baseLiveAgentContentURL: 'https://c.la13-core2.sfdc-lywfpd.salesforceliveagent.com/content',
						deploymentId: '{$atts['sf_deploy_id']}',
						buttonId: '{$atts['sf_chat_id']}',
						baseLiveAgentURL: 'https://d.la13-core2.sfdc-lywfpd.salesforceliveagent.com/chat',
						eswLiveAgentDevName: '{$atts['sf_agent_dev']}',
						isOfflineSupportEnabled: false
					}
				);
			};
		}

		// Load chat script when document is ready
		document.addEventListener('DOMContentLoaded', function() {
			window.initSalesforceChat();

			// Add CSS to initially hide the help button until we know agent status
			var style = document.createElement('style');
			style.textContent = '.embeddedServiceHelpButton { display: none; }';
			document.head.appendChild(style);
		});
	</script>";

	// Return the complete shortcode output
	return $css . $button_html . $script;
}

// Register the shortcode
add_shortcode('sf_chat', 'lvl_salesforce_chat_shortcode');

/**
 * Shortcode for AI Quiz
 *
 * Usage: [ai_quiz]
 */
function lvl_ai_quiz_shortcode($atts)
{
	ob_start();
	?>
	<div class="content">
		<div id="init1" class="init"></div>
		<div id="action" class="container py-5">
			<div class="row align-items-center">
				<div class="col-md-6 text-center">
					<img src="https://www.irafinancialgroup.com/wp-content/uploads/2024/03/Frame-6554.png" alt="Imagem" class="img-fluid">
				</div>
				<div class="col-md-6 text-center">
					<h2 class="mb-4">Let AI pick your perfect Self-Directed solution!</h2>
					<button class="btn btn-primary btn-lg" onclick="showQuestion(1)">Start Here</button>
				</div>
			</div>
		</div>
		<div id="questions" class="container py-5" style="display: none;">
			<div id="question1" class="question">
				<p class="h4">1. What type of Self-Directed IRA or 401(k) investment do you want to do?</p>
				<p class="h5">Please select an option below:</p>
				<div class="row mt-4">
					<?php
					$options = [
							'Real estate', 'Hedge fund', 'Real estate fund', 'Private equity',
							'Private business (less than 50%)', 'Private business (over 50%)',
							'Venture capital', 'Carried interest', 'Founder stock',
							'Precious metals', 'Cryptos', 'Other',
					];
					foreach ($options as $option) {
						echo '<div class="col-md-4 mb-3">
										<button class="btn btn-primary w-100" onclick="showQuestion(2, \'' . $option . '\')">' . $option . '</button>
									</div>';
					}
					?>
				</div>
				<p class="text-center mt-4">
					<a href="javascript:void(0)" class="btn btn-secondary" onclick="showQuestion(1)">Restart</a>
				</p>
			</div>
		</div>
	</div>
	<script>
      const questionsContainer = document.getElementById('questions');
      const actionContainer = document.getElementById('action');

      function closeQuiz() {
          actionContainer.style.display = 'block';
          questionsContainer.style.display = 'none';
          questionsContainer.innerHTML = ''; // Clear dynamically added questions
      }

      function showQuestion(step, answer1 = '', answer2 = '') {
          // Hide the action container and show the questions container
          actionContainer.style.display = 'none';
          questionsContainer.style.display = 'block';

          // Reset the state for step 1
          if (step === 1) {
              // Clear dynamically added content
              questionsContainer.innerHTML = `
                                  <div id="question1" class="question">
                                      <p class="h4">1. What type of Self-Directed IRA or 401(k) investment do you want to do?</p>
                                      <p class="h5">Please select an option below:</p>
                                      <div class="row mt-4">
                                          ${['Real estate', 'Hedge fund', 'Real estate fund', 'Private equity',
                  'Private business (less than 50%)', 'Private business (over 50%)',
                  'Venture capital', 'Carried interest', 'Founder stock',
                  'Precious metals', 'Cryptos', 'Other']
                  .map(option => `
                                                  <div class="col-md-4 mb-3">
                                                      <button class="btn btn-primary w-100" onclick="showQuestion(2, '${option}')">${option}</button>
                                                  </div>
                                              `).join('')}
                                      </div>
                                      <p class="text-center mt-4">
                                          <a href="javascript:void(0)" class="btn btn-secondary" onclick="closeQuiz()">Close Quiz</a>
                                      </p>
                                  </div>
                              `;
          } else if (step === 2) {
              renderQuestion2(answer1);
          } else if (step === 3) {
              renderQuestion3(answer1, answer2);
          } else if (step === 'result') {
              renderResult(answer1, answer2);
          }
      }

      function renderQuestion2(answer1) {
          questionsContainer.innerHTML = `
                              <div class="question">
                                  <p class="h4">2. Are you self-employed or have a small business with no full-time employees other than the owners?</p>
                                  <p class="h5">Please select an option below:</p>
                                  <div class="row mt-4">
                                      <div class="col-md-6 mb-3">
                                          <button class="btn btn-primary w-100" onclick="showQuestion(3, '${answer1}', 'Yes')">Yes</button>
                                      </div>
                                      <div class="col-md-6 mb-3">
                                          <button class="btn btn-primary w-100" onclick="showQuestion(3, '${answer1}', 'No')">No</button>
                                      </div>
                                  </div>
                                  <p class="text-center mt-4">
                                  <a href="javascript:void(0)" class="btn btn-secondary" onclick="showQuestion(1)">Restart</a>
                                  </p>
                              </div>
                          `;
      }

      function renderQuestion3(answer1, answer2) {
          const useLLC = ['Real estate', 'Hedge fund', 'Real estate fund', 'Private equity', 'Private business (less than 50%)', 'Venture capital', 'Carried interest', 'Founder stock', 'Precious metals', 'Other'];
          if (answer2 === 'No' && useLLC.includes(answer1)) {
              questionsContainer.innerHTML = `
                                  <div class="question">
                                      <p class="h4">3. Do you want to use an LLC to make IRA investments to gain more control and benefit from limited liability protection?</p>
                                      <p class="h5">Please select an option below:</p>
                                      <div class="row mt-4">
                                          <div class="col-md-6 mb-3">
                                              <button class="btn btn-primary w-100" onclick="showQuestion('result', '${answer1}', '${answer2}', 'Yes')">Yes</button>
                                          </div>
                                          <div class="col-md-6 mb-3">
                                              <button class="btn btn-primary w-100" onclick="showQuestion('result', '${answer1}', '${answer2}', 'No')">No</button>
                                          </div>
                                      </div>
                                      <p class="text-center mt-4">
                                      <a href="javascript:void(0)" class="btn btn-secondary" onclick="showQuestion(1)">Restart</a>
                                      </p>
                                  </div>
                              `;
          } else {
              showQuestion('result', answer1, answer2);
          }
      }

      function renderResult(answer1, answer2, answer3 = '') {
          let resultHTML = '';
          if (answer2 === 'Yes' && ['Real estate', 'Hedge fund', 'Real estate fund', 'Private equity', 'Private business (less than 50%)', 'Venture capital', 'Carried interest', 'Founder stock', 'Precious metals', 'Other'].includes(answer1)) {
              resultHTML = `
                                  <div class="text-center">
                                      <h2>Solo 401(k) suits you the best!</h2>
                                      <p>IRA Financial offers an open-architecture Solo 401(k)...</p>
                                  </div>
                              `;
          } else if (answer1 === 'Private business (over 50%)') {
              resultHTML = `
                                  <div class="text-center">
                                      <h2>ROBS suits you the best!</h2>
                                      <p>Use your retirement funds to legally invest in a new or existing business...</p>
                                  </div>
                              `;
          } else if (answer1 === 'Cryptos') {
              resultHTML = `
                                  <div class="text-center">
                                      <h2>IRAfiCrypto suits you the best!</h2>
                                      <p>Hold your cryptos on an exchange...</p>
                                  </div>
                              `;
          } else if (answer3 === 'Yes') {
              resultHTML = `
                                  <div class="text-center">
                                      <h2>Self-Directed IRA LLC suits you the best!</h2>
                                      <p>With a Self-Directed IRA LLC...</p>
                                  </div>
                              `;
          } else {
              resultHTML = `
                                  <div class="text-center">
                                      <h2>Self-Directed IRA suits you the best!</h2>
                                      <p>Self-Directed IRA is a type of individual retirement account...</p>
                                  </div>
                              `;
          }

          questionsContainer.innerHTML = `
                              <div class="result">
                                  ${resultHTML}
                                  <div class="mt-4">
                                      <button class="btn btn-primary" onclick="showQuestion(1)">Restart</button>
                                  </div>
                              </div>
                          `;
      }
	</script>
	<?php
	return ob_get_clean();
}

add_shortcode('ai_quiz', 'lvl_ai_quiz_shortcode');


/**
 * Shortcode to process [elementor-template] when Elementor is not active
 *
 * Usage: [fallback-elementor-template id="383"]
 */
function lvl_fallback_elementor_template_shortcode($atts)
{
	// Extract attributes
	$atts = shortcode_atts(array(
			'id' => 0,
	), $atts);


	// If Elementor is active, use the native shortcode
	if (defined('ELEMENTOR_VERSION')) {
		return do_shortcode('[elementor-template id="' . esc_attr($atts['id']) . '"]');
	}

	// If Elementor is not active, try to get the content directly
	$template_id = absint($atts['id']);
	if (!$template_id) {
		return '';
	}

	// if ID = 383 then direct replacement with pattern
	if ($atts['id'] == 383) {
		return apply_filters('the_content','<!-- wp:block {"ref":2605,"bs":{"theme":""}} /-->');
	} else {
		// Don't process if ID is not 383
		return "";
	}

	// Get template content
	$template = get_post($template_id);
	if (!$template || 'publish' !== $template->post_status) {
		return '';
	}

	// Get the raw content
	$content = $template->post_content;

	// Process shortcodes in the content
	$content = do_shortcode($content);

	// Apply filters
	$content = apply_filters('the_content', $content);

	// remove SVGs
	$content = preg_replace('/<svg.*?<\/svg>/s', '', $content);

	return '<div class="elementor-template-wrapper elementor-template-' . $template_id . '">' . $content . '</div>';
}

add_shortcode('fallback-elementor-template', 'lvl_fallback_elementor_template_shortcode');

/**
 * Backward compatibility: Allow using the standard [elementor-template] shortcode
 * when Elementor is not active
 */
function lvl_process_elementor_template_shortcode($atts)
{
	if (!defined('ELEMENTOR_VERSION')) {
		return lvl_fallback_elementor_template_shortcode($atts);
	}
	return ''; // Let Elementor handle it when active
}

add_shortcode('elementor-template', 'lvl_process_elementor_template_shortcode');