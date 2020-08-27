<?php


class GlobalChat {

  private $card_start = '<div class="card direct-chat direct-chat-primary">';

  private $card_end = '</div>';

  private $card_header = '<div class="card-header">
                <h3 class="card-title">Global Chat</h3>
              </div>';

  private $card_body_start = '<div class="card-body">';

  private $card_body_end = '</div>';

  private $messages_container_start = '<div class="direct-chat-messages">';

  private $messages_container_end = '</div>';

  private $message_raw_own = '<div class="direct-chat-msg right">
                                <div class="direct-chat-infos clearfix">
                                  <span class="direct-chat-name float-right">{{user-full-name}}</span>
                                  <span class="direct-chat-timestamp float-left">{{message-date-time}}</span>
                                </div>
                                <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{user-full-name}}&size=128" alt="{{user-full-name}} image">
                                <div class="direct-chat-text">
                                  {{message-text}}
                                </div>
                              </div>';

  private $message_raw_other = '<div class="direct-chat-msg">
                                <div class="direct-chat-infos clearfix">
                                  <span class="direct-chat-name float-left">{{user-full-name}}</span>
                                  <span class="direct-chat-timestamp float-right">{{message-date-time}}</span>
                                </div>
                                <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{user-full-name}}&size=128" alt="{{user-full-name}} image">
                                <div class="direct-chat-text">
                                  {{message-text}}
                                </div>
                              </div>';

  private $card_footer = '<div class="card-footer">
                          <form action="#" method="post">
                            <div class="input-group">
                              <input type="text" id="global-chat-send-msg-box" name="message" placeholder="Type Message ..." class="form-control">
                              <span class="input-group-append">
                                <button onclick="sendMessage()" type="button" class="btn btn-primary">Send</button>
                              </span>
                            </div>
                          </form>
                        </div>';

  private $script_raw = '<script>

                          function sendMessage()
                          {
                            var messageText = $("#global-chat-send-msg-box").val();
                            var sender = "{{user-id}}";

                            $.ajax({
                                    type: "POST",
                                    url: "/modules/core/global_chat/send_message.php",
                                    data: {
                                      messageText : messageText,
                                      sender : sender
                                    },
                                    success: function(data){
                                       location.reload();
                                    },
                                    dataType: "String"
                                  });
                          }

                          </script>';

  private $isOwnMessage;
  private $fullName;
  private $msgDateTime;
  private $msgText;
  private $complete_data;
  private $uid;

  function __construct($uid)
  {
    $this->uid = $uid;
    $this->complete_data = $this->card_start.$this->card_header.$this->card_body_start.$this->messages_container_start;
  }

  private function setMessageValues($isOwnMessage, $fullName, $msgDateTime, $msgText)
  {
    $this->isOwnMessage = $isOwnMessage;
    $this->fullName = $fullName;
    $this->msgDateTime = $msgDateTime;
    $this->msgText = $msgText;
  }

  private function generateMessageHTML()
  {
    $raw_html = ($this->isOwnMessage) ? $this->message_raw_own : $this->message_raw_other;
    $raw_html = str_replace("{{user-full-name}}", $this->fullName, $raw_html);
    $raw_html = str_replace("{{message-date-time}}", $this->msgDateTime, $raw_html);
    $raw_html = str_replace("{{message-text}}", $this->msgText, $raw_html);
    $this->complete_data .= $raw_html;
  }

  public function addMessage($fullName, $msgDateTime, $msgText, $isOwnMessage)
  {
    $this->setMessageValues($isOwnMessage, $fullName, $msgDateTime, $msgText);
    $this->generateMessageHTML();
  }

  public function getHTML()
  {
    $script_html = str_replace("{{user-id}}", $this->uid, $this->script_raw);
    $this->complete_data .= $this->messages_container_end.$this->card_body_end.$this->card_footer.$this->card_end.$script_html;
    return $this->complete_data;
  }

}


?>

